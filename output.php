<?php

require_once './database/database.php';

$cardsTable = "select * from card_info";
$docsTable = "select * from docs";

$cards;
$docs;
$stmt = $pdo->prepare($cardsTable);
if (isset($stmt)) {
    $stmt->execute();
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $pdo->prepare($docsTable);
    $stmt->execute();
    $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Таблицы</title>
</head>

<body>
    <table style="border: 1px solid #000; margin-bottom: 20px;">
        <thead>
            <tr>
                <th>Номер</th>
                <th>Организация</th>
                <th>Ссылка</th>
                <th>Статус</th>
                <th>Дата начала</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cards as $card): ?>
                <tr>
                    <td style="border: 1px solid #000;">
                        <?= $card['number'] ?>
                    </td>
                    <td style="border: 1px solid #000;">
                        <?= $card['name_org'] ?>
                    </td >
                    <td style="border: 1px solid #000;">
                        <?= $card['link'] ?>
                    </td>
                    <td style="border: 1px solid #000;">
                        <?= $card['status'] ?>
                    </td>
                    <td style="border: 1px solid #000;">
                        <?= $card['date'] ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table style="border: 1px solid #000;">
        <thead>
            <tr>
                <th>Имя документа</th>
                <th>Ссылка документа</th>
                <th>Ссылка на карточку</th>
                
            </tr>
        </thead>
        <tbody>
            <?php foreach ($docs as $doc): ?>
                <tr>
                    <td style="border: 1px solid #000;">
                        <?= $doc['name_doc'] ?>
                    </td>
                    <td style="border: 1px solid #000;">
                        <?= $doc['link'] ?>
                    </td >
                    <td style="border: 1px solid #000; text-align:center;">
                        <?= $doc['id_card_info'] ?>
                    </td>
                    
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>