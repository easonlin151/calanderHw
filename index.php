<!DOCTYPE HTML>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf 8">
    <title>PHP日曆</title>
    <link rel="stylesheet" href="./style.css">
</head>

<body onload="current_time()">
    <form action='./index.php' method='GET'>


        <?php

        require_once 'calendar.php';
        date_default_timezone_set('Asia/Taipei');
        //new一個calendar類
        $util = new Calendar();
        //年份數組和月份數組

        for ($i = $_GET['array_years'] - 100; $i < $_GET['array_years'] + 100; $i++) {
            $years[] = $i;
        }



        $months = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
        //獲取選擇的年份
        //如果沒有提交POST請求，則返回當年年份,否則，返回選擇的年份
        if (empty($_GET['array_years'])) {
            $year = date("Y");
        } else {
            $year = $_GET["array_years"];
        }
        //如果沒有提交POST請求，則返回當前月份,否則，返回選擇的月份
        if (empty($_GET['array_months'])) {
            $month = date("n");
        } else {
            $month = $_GET["array_months"];
        }

        $calendar = $util->threshold($year, $month); //獲取各個邊界值
        $caculate = $util->caculate($calendar); //計算日曆的天數與樣式
        $draws = $util->draw($caculate); //畫表格，設置table中的tr與td
        $poetry = $util->poetry(); //輸出文字
        $switchYear = $util->switchYear($year, $month);

        $prevMonth = $switchYear['prevMonth'];
        $prevYear = $switchYear['prevYear'];
        $nextMonth = $switchYear['nextMonth'];
        $nextYear = $switchYear['nextYear'];
        $year = $switchYear['year'];
        $month = $switchYear['month'];
        $monthFont = date("M", strtotime($switchYear['monthFont']));
        $yearFont = $year;


        ?>


        <div class="main">

            <div class=section>
                <div class="pic">
                    <img src="https://picsum.photos/333/480" alt="">
                </div>
                <div class="box">

                    <div class="container">
                        <div id="dycalendar">
                            <div class="dycalendar-month-container">

                                <div class="dycalendar-header">
                                    <span class="dycalendar-prev-next-btn prev-btn">
                                        <a
                                            href="index.php?array_years=<?= $prevYear; ?>&array_months=<?= $prevMonth; ?>">
                                            &lt;</a>
                                    </span>

                                    <span class="dycalendar-span-month-year">
                                        <?php echo "$monthFont" . " " . "$year"; ?></span>
                                    <span class="dycalendar-prev-next-btn next-btn">
                                        <a
                                            href="index.php?array_years=<?= $nextYear; ?>&array_months=<?= $nextMonth; ?>">
                                            &gt;</a>
                                    </span>
                                    <table class="test123">
                                        <!--選擇年份-->
                                        <select name="array_years" class="selected-year">
                                            <?php foreach ($years as $data) { ?>
                                            <option value="<?= $data; ?>"
                                                <?php if ($year == $data) echo 'selected="selected"' ?>>
                                                <?php echo $data ?></option>
                                            <?php } ?>
                                        </select>
                                        <!--選擇月份-->
                                        <select name="array_months" class="selected-month">
                                            <?php foreach ($months as $data) { ?>
                                            <option value="<?php echo $data ?>"
                                                <?php if ($month == $data) echo 'selected="selected"' ?>>
                                                <?php echo $data ?></option>
                                            <?php } ?>
                                        </select>
                                        <input class="selected-submit" type="submit" value="送出" />
                                    </table>
                                    <div class="dycalendar-body">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td style="color:red;">S</td>
                                                    <td>M</td>
                                                    <td>T</td>
                                                    <td>W</td>
                                                    <td>T</td>
                                                    <td>F</td>
                                                    <td style="color:red;">S</td>
                                                </tr>
                                                <?php

                                                foreach ($draws as $draw) { ?>
                                                <tr>
                                                    <?php foreach ($draw as $date) {
                                                            echo "<td class='{$date['tdclass']} {$date['chktoday']} festivalDay{$date['festivalDay']} {$date['pclass']}'>
                                                {$date['day']}
                                                </td>";
                                                        } ?>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bottom">
                        <?php echo $poetry ?>
                    </div>
                </div>
            </div>
        </div>



    </form>
    <script>
    function current_time() {
        NowDate = new Date();
        h = NowDate.getHours();
        m = NowDate.getMinutes();
        s = NowDate.getSeconds();
        document.getElementById('current').innerHTML = '現在時刻:' + h + '時' + m + '分' + s + '秒';
        setTimeout('current_time()', 1000);
    }
    </script>
</body>

</html>