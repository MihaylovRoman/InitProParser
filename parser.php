<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parser</title>
</head>

<body>

    <?php
    require_once "./database/database.php";
    require_once __DIR__ . "/phpQuery-onefile.php";
    function changePage($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    $result = changePage("https://com.ru-trade24.ru/Home/Trades");
    $pq = phpQuery::newDocument($result);

    $filtersData = array();

    $filters = $pq->find('.select__el');
    foreach ($filters as $filter) {
        $filtersData[] = pq($filter)->text();
    }
    $needFilter = $filtersData[7];

    $cards = $pq->find('.trade-card');
    $numberProcedures = $pq->find('.trade-card__type');
    $nameOrgs = $pq->find('.trade-card__name');
    $cardsLink = $pq->find('.trade-card a');
    $cardsStatus = $pq->find('.trade-card__status');
    
    foreach ($cards as $card => $value) {
        $pqCard = pq($value);

        $number = intval(preg_replace('/[^0-9]/', '', ($pqCard->find('.trade-card__type')->text())));
        $nameOrg = $pqCard->find('.trade-card__name')->text();
        $link = $pqCard->find('a')->attr('href');
        $status = $pqCard->find('.trade-card__status')->text();

        if ($status == $needFilter) {
            $cardInfo[$card]['number'] = $number;
            $cardInfo[$card]['nameOrg'] = $nameOrg;
            $cardInfo[$card]['link'] = "https://com.ru-trade24.ru" . $link;
            $cardInfo[$card]['status'] = $status;
        }
    }
    
    for ($i = 0; $i<count($cardInfo); $i++) {

        $newLinkPage = changePage($cardInfo[$i]['link']);
        $pqNewPage = phpQuery::newDocument($newLinkPage);
        
        $ArrDate = array();
        $dateStart = $pqNewPage->find('.info__title');
        foreach ($dateStart as $date) {
            $ArrDate[] = pq($date)->text();
        }

        $arrLinksDoc = array();
        $docs = $pqNewPage->find('.doc');
        foreach ($docs as $doc) {
            $arrLinksDoc[] = array(
                'name' => pq($doc)->text(),
                'link' => "https://com.ru-trade24.ru" . pq($doc)->attr('href')
            );
        }

        $cardInfo[$i]['dateStart'] = $ArrDate[26];
        $cardInfo[$i]['docLinks'] = $arrLinksDoc;

        $checkerCard = $pdo->prepare("SELECT * FROM card_info WHERE link = :link");
        $checkCard = $checkerCard->execute(['link' => $cardInfo[$i]['link']]);
        
        if ($checkCard == 1) {
            $stmt = $pdo->prepare("insert into card_info(number,name_org,link,status,date) values(?,?,?,?,?)");
            $stmt->execute([
                $cardInfo[$i]['number'],
                $cardInfo[$i]['nameOrg'],
                $cardInfo[$i]['link'],
                $cardInfo[$i]['status'],
                $cardInfo[$i]['dateStart']
            ]);

            $lastId = $pdo->lastInsertId();
            $stmt = $pdo->prepare('insert into docs(name_doc, link, id_card_info) values(?,?,?) ');
            for ($j = 0; $j < count($arrLinksDoc); $j++) {
                $stmt->execute([
                    $arrLinksDoc[$j]['name'],
                    $arrLinksDoc[$j]['link'],
                    $lastId
                ]);
            }
        }

    }

    echo "<pre>";
    var_dump($cardInfo);
    echo "</pre>";
    ?>
</body>

</html>