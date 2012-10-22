<?php
/*
#Copyright (c) 2012 Remy van Elst
#Permission is hereby granted, free of charge, to any person obtaining a copy
#of this software and associated documentation files (the "Software"), to deal
#in the Software without restriction, including without limitation the rights
#to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
#copies of the Software, and to permit persons to whom the Software is
#furnished to do so, subject to the following conditions:
#
#The above copyright notice and this permission notice shall be included in
#all copies or substantial portions of the Software.
#
#THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
#IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
#FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
#AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
#LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
#OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
#THE SOFTWARE.
*/

$hostlist=array(
                'raymii.org.json' => 'http://stat.raymii.org/stat.json',
                'raymii.nl.json' => 'http://raymii.nl/stat.json',
                'vps1.spcs.json' => 'http://vps1.sparklingclouds.nl/stat.json',
                'vps3.spcs.json' => 'http://vps3.sparklingclouds.nl/stat.json',
                'vps5.spcs.json' => 'http://vps5.sparklingclouds.nl/stat.json',
                'vps8.spcs.json' => 'http://vps8.sparklingclouds.nl/stat.json',
                'vps11.spcs.json' => 'http://vps11.sparklingclouds.nl/stat.json',
                'vps12.spcs.json' => 'http://vps12.sparklingclouds.nl/stat.json',
                'vps13.spcs.json' => 'http://vps13.sparklingclouds.nl/stat.json',
                'vps12.spcs.json' => 'http://vps14.sparklingclouds.nl/stat.json'
                );
$pinglist = array('raymii.nl',
                  'erasmusmc.nl',
                  'google.com',
                  'raymii.org',
                  'tweakers.net',
                  'lowendbox.com' 
                  );
#Set this or your logs will fill up your disk.
date_default_timezone_set('Europe/Amsterdam');


?>



<html>
<head>
    <title>Stats</title>
    <!-- bar via http://www.joshuawinn.com/quick-and-simple-css-percentage-bar-using-php/ -->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
    <!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <script type="text/javascript" src="git/js/prettify.js"></script>                                   <!-- PRETTIFY -->
    <script type="text/javascript" src="git/js/kickstart.js"></script>                                  <!-- KICKSTART -->
    <link rel="stylesheet" type="text/css" href="git/css/kickstart.css" media="all" />                  <!-- KICKSTART -->
    <link rel="stylesheet" type="text/css" href="git/css/style.css" media="all" />                      <!-- CUSTOM STYLES -->
    <meta http-equiv="refresh" content="300">
    <script type="text/javascript">
    <!--
        $(document).ready(function() {
            var showText="Show";
            var hideText="Hide";
            $(".toggle").prev().append(' (<a href="#" class="toggleLink">'+showText+'</a>)');
            $('.toggle').hide();
            $('a.toggleLink').click(function() {
                if ($(this).html()==showText) {
                    $(this).html(hideText);
                }
                else {
                    $(this).html(showText);
                }
                $(this).parent().next('.toggle').toggle('slow');
                return false;
            });
        });
        //-->
    </script>
    <style type="text/css">
        .percentbar { background:#CCCCCC; border:1px solid #666666; height:10px; }
        .percentbar div { background: #28B8C0; height: 10px; }
    </style>
</head>
<body><a id="top-of-page"></a><div id="wrap" class="clearfix">
<?php
function historystat($bestand,$host) {
    $files=array();
    if ($handle = opendir('./history/')) {
        while (false !== ($entry = readdir($handle))) {
            $files[]=$entry;
        }
        closedir($handle);
    } else {
        echo "Error: cannot open direcotry './history' for file $bestand from host $host.";
    }
    rsort($files);
    foreach ($files as $key => $value) {
        $filename = explode(".", $value);
        $amount = count($filename);
        $timestamp = end($filename);
        $amount1 = intval($amount)-1;
        $bestandnaam = str_replace(",",".",implode(",",array_slice($filename, 0, $amount1)));
        if($bestandnaam == $bestand) {
            $jsonfile = "./history/" . $value;
            if ($jsonopen = file_get_contents($jsonfile)) {
             if (json_decode($jsonopen, true)) {
                $filedate = date("d.m.Y - H:i:s", $timestamp);
                echo "<h4>Date: $filedate</h4>\n";
                echo "<table>";
                linestat($jsonfile,$host);
                echo "</table><br />";
            } else {
                echo "Cannot decode json file $jsonfile.";
            }
        } else {
            echo "Cannot open json file $jsonfile.";
        }
    }
}   
}

function shortstat($bestand,$host) {

    echo "<table class=\"striped\">";
    linestat($bestand,$host);
    echo "</table><br />";
}

function percentagebar($percentage) {

    $percentage = str_replace("%", "",$percentage); 
    echo "<center>".$percentage . "%</center>";
    echo "<div class=\"percentbar\" style=\"width: 100px;\">";
    echo "<div style=\"width:".round($percentage)."px;\">";
    echo "</div></div>";
}

function savefile($bestand,$naam){

    $curdir=getcwd();
    if(!is_dir("{$curdir}/history")){
        mkdir("${curdir}/history") or die("Cannot create history folder. Create it manually and make sure the webserver can write to it.");
    } else {
        $DATETIME=date('U');
        $DATEHOUR=date('H');
        $DATEINT=date('i');
        $DATESEC=date('s');

#if (filemtime("${curdir}/${naam}") < (time() - 300)) {
        if ($DATEINT=="05" || $DATEINT=="10" || $DATEINT=="15" ||  $DATEINT=="20" || $DATEINT=="25" || $DATEINT=="35" || $DATEINT=="40" || $DATEINT=="45" || $DATEINT=="50" || $DATEINT=="55") {
            $local_file=file_get_contents($bestand);
            $saved_local_file=file_put_contents("${curdir}/$naam", $local_file);
            if (md5($local_file) != md5(file_get_contents("${curdir}/${naam}.old"))) {
                file_put_contents("${naam}.old", $local_file);
            }
        } else {
            $local_file=file_get_contents($naam);
        }
        if($DATEINT=="00" || $DATEINT=="30"){
            if($DATESEC == "30" || $DATESEC == "55") {
                file_put_contents("history/${naam}.${DATETIME}", $local_file);
            }
        }
    }

}

function linestat($bestand,$host) {
    if ($file = file_get_contents($bestand)) {
        if ($json_a = json_decode($file, true)) {
            $closed=0;
            $havestat = 0;
            if(is_array($json_a)) {
                ?>

                <tr>
                    <th>Uptime</th>
                    <th>Services</th>
                    <th>Load</th>
                    <th>Users</th>
                    <th>Updates</th>
                    <th>HDD (T/U/F)</th>
                    <th>RAM (T/U/F)</th>
                    <th>NET RX</th>
                    <th>NET TX</th>
                </tr>
                <tr>

                    <td><?php echo str_replace(",", "",$json_a['Uptime']); ?></td>
                    <td><?php
                    foreach ($json_a['Services'] as $service => $status) {
                        if($status == "running") {
                            echo '<font color="green">' . $service . '</font> up. <br /> ';
                        } elseif ($status == "not running") {
                            echo '<font color="red">' . $service . '</font> <b>down.</b> <br /> ';
                        }
                    }
                    ?>
                </td>
                <td><?php echo str_replace(",", "",$json_a['Load']); ?></td>
                <td><?php echo $json_a['Users logged on']; ?></td>
                <td><?php echo $json_a['updatesavail']; ?></td>
                <td><?php
                percentagebar($json_a['Disk']['percentage']);
                echo "<br />";
                echo $json_a['Disk']['total'] . " <br /> "; 
                echo $json_a['Disk']['used'] . " <br /> ";
                echo $json_a['Disk']['free'];
                
                ?>
                </td>    
                <td><?php
                    $used_ram = $json_a['Total RAM'] - $json_a['Free RAM'];
                    $value = $used_ram;
                    $max = $json_a['Total RAM'];
                    $scale = 1.0;
                    if (!empty($max)) {
                        $percent = ($value * 100) / $max;
                    } else {
                        $percent = 0;
                    }
                    if ($percent > 100) {
                        $percent = 100;
                    }
                    percentagebar(round($percent * $scale));
                    echo "<br />";
                    echo $json_a['Total RAM'] . " MB <br /> ";
                    echo $used_ram . " MB <br /> ";
                    echo $json_a['Free RAM'] . " MB";
                    ?>
                </td>
                <td><?php
                    $rxmb=round((($json_a['rxbytes'] / 1024) / 1024));
                   if ($rxmb < 1024) {
                        echo $rxmb . " MB";
                    } elseif ($rxmb < 1024000) {
                        $rxmb = round(($rxmb / 1024),2);
                        echo $rxmb . " GB";
                    } elseif ($rxmb > 1024000) {
                        $rxmb = round((($rxmb / 1024) / 1024),2);
                        echo $rxmb . " TB";   
                    }
                    
                    ?>
                </td>
            <td><?php
                $txmb=round((($json_a['txbytes'] / 1024) / 1024));
                if ($txmb < 1024) {
                        echo $txmb . " MB";
                    } elseif ($txmb < 1024000) {
                        $txmb = round(($txmb / 1024),2);
                        echo $txmb . " GB";
                    } elseif ($txmb > 1024000) {
                        $txmb = round((($txmb / 1024) / 1024),2);
                        echo $txmb . " TB";   
                    }
                ?>
            </td>
        </tr>
    <?php
            }
        } else {
            echo "Error decoding JSON stat file for host $host";
        }
    } else  {
        echo "Error while getting stats for host $host from file $bestand";
    }
}

function ping($host, $port, $timeout) { 
  $tB = microtime(true); 
  $fP = fSockOpen($host, $port, $errno, $errstr, $timeout); 
  if (!$fP) {  return '<font color="red">' . $host . ' DOWN from here. </font>'; } 
  $tA = microtime(true); 
  return '<font color="green">' . $host . ' ' . round((($tA - $tB) * 1000), 0).' ms UP</font>';
}

function dosomething($bestand,$host,$actie){
    if(!empty($bestand) && !empty($host) && !empty($actie)) {
        # this function should be called per item on a foreach loop.
        switch ($actie) {
            case 'shortstat':
            savefile($host,$bestand);
            $parsed_host=parse_url($host,PHP_URL_HOST);
            shortstat($bestand,$parsed_host);
            break;
            case 'historystat':
            savefile($host,$bestand);
            $parsed_host=parse_url($host,PHP_URL_HOST);
            historystat($bestand,$parsed_host);
            break;
        }
    }   
}

?> 
<div class="col_12">
    <ul class="tabs left">
        <li><a href="#tabc1">Overview</a></li>
        <li><a href="#tabc2">History</a></li>
    </ul>
    <div id="tabc1" class="tab-content">
        <?php 
        echo "<i>Ping monitor:</i>";
        foreach ($pinglist as $key => $value) {
            echo ping("$value",80,5) . ", ";
        }
        ?>
        <h4>Server Status</h4>
        <?php
        foreach ($hostlist as $key => $value) {
            $host=parse_url($value,PHP_URL_HOST);
            echo "<h5>Host: ${host}</h6>";
            dosomething($key,$value,"shortstat");
            echo "<hr class=\'alt1\' />";
        }
        ?>
    </div>
    <div id="tabc2" class="tab-content"> 
        <?php
        foreach ($hostlist as $key => $value) {
            $host=parse_url($value,PHP_URL_HOST);
            echo "<p>History for host ${host}</p>\n";
            echo "<div class=\"toggle\">";
            dosomething($key,$value,"historystat");
            echo "</div>";
        }
        ?>
    </div>
</div>
</body>
</html>