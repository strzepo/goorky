<?php
/**
 * convert.php – kliknij ➜ „Pobierz feed XML”
 * 2025-05-25 – kolejność pól + Kod_GTU, teraz CENA BRUTTO zamiast netto
 */
declare(strict_types=1);

const FEED_URL =
  'https://api.ikonka.eu/77fb34a88fe36d294100337700929661bede5248.xml'
  . '?variant=1&lang=pl&currency=PLN';

/*──────────────────── 1.  TRYB POBIERANIA PLIKU ────────────────────*/
if (isset($_GET['download'])) {

    /* 1.1 – pobierz XML hurtowni */
    $xmlIn = @file_get_contents(FEED_URL);
    if ($xmlIn === false) {
        http_response_code(502);
        exit('Błąd: nie mogę pobrać feedu.');
    }

    /* 1.2 – dokument wyjściowy */
    $in  = new SimpleXMLElement($xmlIn);
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->formatOutput = true;
    $root = $dom->appendChild($dom->createElement('Produkty'));

    /* 1.3 – helper CDATA/Text */
    $add = static function (
        DOMElement $parent,
        string     $tag,
        string     $value = '',
        bool       $cdata = true
    ) use ($dom) {
        $el = $parent->appendChild($dom->createElement($tag));
        $el->appendChild(
            $cdata ? $dom->createCDATASection($value)
                   : $dom->createTextNode($value)
        );
    };

    /* 1.4 – funkcja zwracająca cenę NETTO (różne układy feedu) */
    $getNet = static function (SimpleXMLElement $p): float {
        if (isset($p->cena_netto) && trim((string)$p->cena_netto) !== '') {
            return (float)str_replace(',', '.', (string)$p->cena_netto);
        }
        if (isset($p->cena)) {
            $raw = trim((string)$p->cena);
            if ($raw !== '')  return (float)str_replace(',', '.', $raw);
            if (isset($p->cena->netto)
                && trim((string)$p->cena->netto) !== '') {
                return (float)str_replace(',', '.', (string)$p->cena->netto);
            }
            $attr = $p->cena->attributes()->netto ?? '';
            if ((string)$attr !== '') {
                return (float)str_replace(',', '.', (string)$attr);
            }
        }
        if (isset($p->cena_brutto) && trim((string)$p->cena_brutto) !== '') {
            $vat   = (float)str_replace(',', '.', (string)($p->vat ?? '23'));
            $gross = (float)str_replace(',', '.', (string)$p->cena_brutto);
            return $vat > 0 ? $gross / (1 + $vat / 100) : $gross;
        }
        return 0.0;
    };

    /*──────────────── 1.5 – KONWERSJA PRODUKTÓW ────────────────*/
    foreach ($in->produkt as $p) {

        /* ── FILTRY ── */
        $qtyRaw = (string)($p->stan ?? '0');
        $qtyVal = (float)str_replace(',', '.', $qtyRaw);
        if ($qtyVal < 1) continue;

        $net = $getNet($p);
        if ($net <= 0) continue;

        /* podatki */
        $vatVal = (float)str_replace(',', '.', (string)($p->vat ?? '23'));
        $gross  = $net * (1 + $vatVal / 100);

        /* ── budowa produktu w podanej kolejności ── */
        $prod = $root->appendChild($dom->createElement('Produkt'));

        $add($prod, 'Kategoria',            (string)$p->kategoria);
        $add($prod, 'Ilosc_produktow',      $qtyRaw,                      false);
        $add($prod, 'Waga',                 (string)$p->waga,             false);
        $add($prod, 'Kod_producenta',       (string)$p->kod);
        $add($prod, 'Informacja_o_bezpieczenstwie',
                                   (string)$p->safety_text);
        $add($prod, 'Kod_ean',              (string)$p->kod_kreskowy);
        $add($prod, 'Kod_GTU',              (string)($p->kod_gtu ?? 'brak'));
        $add($prod, 'Podatek_Vat',
             number_format($vatVal, 2, '.', ''),                         false);
        /* === #9: CENA BRUTTO === */
        $add($prod, 'Cena_brutto',
             number_format($gross, 2, '.', ''),                          false);

        /* Zdjęcia – zbierz linki (3 warianty) */
        $links = [];
        foreach ($p->zdjecia->zdjecie_link ?? [] as $l) {
            $l = trim((string)$l);
            if ($l !== '') $links[] = $l;
        }
        foreach ($p->zdjecia->zdjecie ?? [] as $z) {
            $link  = trim((string)$z->link);
            $plain = trim((string)$z);
            if ($link  !== '') $links[] = $link;
            if ($plain !== '' && !in_array($plain, $links, true)) $links[] = $plain;
        }

        $add($prod, 'Zdjecie_glowne', $links[0] ?? '', false);
        $add($prod, 'Nazwa_produktu', (string)$p->nazwa);
        $add($prod, 'Opis',           trim((string)$p->opis));
        $add($prod, 'Opis_krotki',    (string)$p->opis_krotki);
        $add($prod, 'Rozmiar_pojemnosc', (string)$p->objetosc);
        $add($prod, 'Producent',      (string)$p->producer_id);

        if (count($links) > 1) {
            $extra = $prod->appendChild($dom->createElement('Zdjecia_dodatkowe'));
            foreach (array_slice($links, 1) as $url) {
                $z = $extra->appendChild($dom->createElement('Zdjecie'));
                $add($z, 'Zdjecie_link', $url, false);
            }
        }

        if ((string)$p->zdp === 'Nie') {
            $add($prod, 'Paczkomaty_gabaryt', 'B');
            $add($prod, 'Paczkomaty_ilosc',   '1', false);
        }
    }

    /* 1.6 – wyślij plik */
    $filename = 'feed_do_sklepu_' . date('Ymd_His') . '.xml';
    header('Content-Type: application/xml; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $dom->saveXML();
    exit;
}

/*──────────────────── 1a.  TRYB POBIERANIA FEEDU AKTUALIZACYJNEGO ────────────────────*/
if (isset($_GET['update'])) {
    $xmlIn = @file_get_contents(FEED_URL);
    if ($xmlIn === false) {
        http_response_code(502);
        exit('Błąd: nie mogę pobrać feedu.');
    }

    $in  = new SimpleXMLElement($xmlIn);
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->formatOutput = true;
    $root = $dom->appendChild($dom->createElement('Produkty'));

    $getNet = static function (SimpleXMLElement $p): float {
        if (isset($p->cena_netto) && trim((string)$p->cena_netto) !== '') {
            return (float)str_replace(',', '.', (string)$p->cena_netto);
        }
        if (isset($p->cena)) {
            $raw = trim((string)$p->cena);
            if ($raw !== '')  return (float)str_replace(',', '.', $raw);
            if (isset($p->cena->netto) && trim((string)$p->cena->netto) !== '') {
                return (float)str_replace(',', '.', (string)$p->cena->netto);
            }
            $attr = $p->cena->attributes()->netto ?? '';
            if ((string)$attr !== '') {
                return (float)str_replace(',', '.', (string)$attr);
            }
        }
        if (isset($p->cena_brutto) && trim((string)$p->cena_brutto) !== '') {
            $vat   = (float)str_replace(',', '.', (string)($p->vat ?? '23'));
            $gross = (float)str_replace(',', '.', (string)$p->cena_brutto);
            return $vat > 0 ? $gross / (1 + $vat / 100) : $gross;
        }
        return 0.0;
    };

    $add = static function (
        DOMElement $parent,
        string     $tag,
        string     $value = '',
        bool       $cdata = true
    ) use ($dom) {
        $el = $parent->appendChild($dom->createElement($tag));
        $el->appendChild(
            $cdata ? $dom->createCDATASection($value)
                   : $dom->createTextNode($value)
        );
    };

    foreach ($in->produkt as $p) {
        $qtyRaw = (string)($p->stan ?? '0');
        $qtyVal = (float)str_replace(',', '.', $qtyRaw);
        if ($qtyVal < 0) continue;

        $net = $getNet($p);
        if ($net <= 0) continue;

        $vatVal = (float)str_replace(',', '.', (string)($p->vat ?? '23'));
        $gross  = $net * (1 + $vatVal / 100);

        $prod = $root->appendChild($dom->createElement('Produkt'));
        $add($prod, 'Kod_producenta',  (string)$p->kod);
        $add($prod, 'Ilosc_produktow', $qtyRaw, false);
        $add($prod, 'Cena_brutto',     number_format($gross, 2, '.', ''), false);
    }

    $filename = 'feed_aktualizacja_' . date('Ymd_His') . '.xml';
    header('Content-Type: application/xml; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $dom->saveXML();
    exit;
}

/*──────────────────── 2.  STRONA Z PRZYCISKIEM ────────────────────*/
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="utf-8">
<title>Pobierz feed XML</title>
<style>
  body{margin:0;display:flex;justify-content:center;align-items:center;height:100vh;font-family:system-ui,Arial,sans-serif;background:#f5f7fa}
  button{font-size:1.2rem;padding:.9rem 2.4rem;border:0;border-radius:9px;cursor:pointer;box-shadow:0 3px 6px rgba(0,0,0,.15);}
  button:hover{box-shadow:0 4px 10px rgba(0,0,0,.2);}
</style>
</head>
<body>
<div style="display: flex;">
  <form action="" method="get">
    <input type="hidden" name="download" value="1">
    <button type="submit">Pobierz feed XML</button>
  </form>
  <form action="" method="get" style="margin-left: 20px;">
    <input type="hidden" name="update" value="1">
    <button type="submit">Pobierz feed aktualizacyjny</button>
  </form>
</div>
</body>
</html>