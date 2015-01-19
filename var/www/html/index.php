<?php
     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
?>     
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title>Streaming Monitor - FDL</title>

<link href="css/styles.css" rel="stylesheet" type="text/css" />
<!--[if IE]> <link href="css/ie.css" rel="stylesheet" type="text/css"> <![endif]-->
<script src="/fdl/jwplayer.js"></script>
<script>jwplayer.key="zYUzlLKZYyvvBzT13IPX1XDX5rCXrr4QzOkjBTtSdJA=";</script>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script> 
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/plugins/charts/excanvas.min.js"></script>
<script type="text/javascript" src="js/plugins/charts/jquery.flot.js"></script>
<script type="text/javascript" src="js/plugins/charts/jquery.flot.orderBars.js"></script>
<script type="text/javascript" src="js/plugins/charts/jquery.flot.pie.js"></script>
<script type="text/javascript" src="js/plugins/charts/jquery.flot.resize.js"></script>
<script type="text/javascript" src="js/plugins/charts/jquery.sparkline.min.js"></script>
<script type="text/javascript" src="js/plugins/tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="js/plugins/tables/jquery.sortable.js"></script>
<script type="text/javascript" src="js/plugins/tables/jquery.resizable.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.autosize.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.uniform.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.inputlimiter.min.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.tagsinput.min.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.autotab.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.select2.min.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.dualListBox.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.cleditor.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.ibutton.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.validationEngine-en.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.validationEngine.js"></script>
<script type="text/javascript" src="js/plugins/uploader/plupload.js"></script>
<script type="text/javascript" src="js/plugins/uploader/plupload.html4.js"></script>
<script type="text/javascript" src="js/plugins/uploader/plupload.html5.js"></script>
<script type="text/javascript" src="js/plugins/uploader/jquery.plupload.queue.js"></script>
<script type="text/javascript" src="js/plugins/wizards/jquery.form.wizard.js"></script>
<script type="text/javascript" src="js/plugins/wizards/jquery.validate.js"></script>
<script type="text/javascript" src="js/plugins/wizards/jquery.form.js"></script>
<script type="text/javascript" src="js/plugins/ui/jquery.collapsible.min.js"></script>
<script type="text/javascript" src="js/plugins/ui/jquery.breadcrumbs.js"></script>
<script type="text/javascript" src="js/plugins/ui/jquery.tipsy.js"></script>
<script type="text/javascript" src="js/plugins/ui/jquery.progress.js"></script>
<script type="text/javascript" src="js/plugins/ui/jquery.timeentry.min.js"></script>
<script type="text/javascript" src="js/plugins/ui/jquery.colorpicker.js"></script>
<script type="text/javascript" src="js/plugins/ui/jquery.jgrowl.js"></script>
<script type="text/javascript" src="js/plugins/ui/jquery.fancybox.js"></script>
<script type="text/javascript" src="js/plugins/ui/jquery.fileTree.js"></script>
<script type="text/javascript" src="js/plugins/ui/jquery.sourcerer.js"></script>
<script type="text/javascript" src="js/plugins/others/jquery.fullcalendar.js"></script>
<script type="text/javascript" src="js/plugins/others/jquery.elfinder.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.mousewheel.js"></script>
<script type="text/javascript" src="js/plugins/ui/jquery.easytabs.min.js"></script>
<script type="text/javascript" src="js/files/bootstrap.js"></script>
<script type="text/javascript" src="js/files/functions.js"></script>
<script type="text/javascript" src="js/charts/chart.js"></script>
<script type="text/javascript" src="js/charts/bar.js"></script>
<script type="text/javascript" src="js/charts/hBar.js"></script>
<script type="text/javascript" src="js/charts/updating.js"></script>
<script type="text/javascript" src="js/charts/pie.js"></script>
<script type="text/javascript" src="js/charts/chart_side.js"></script>
<script type="text/javascript" src="js/charts/bar_side.js"></script>
<script type="text/javascript" src="js/charts/hBar_side.js"></script>
          <script type="text/javascript">
          $(document).ready(function(){
             $.plot($("#delsplaceholder"), [ [[0, 0], [1, 1]] ], { yaxis: { max: 1 } });
          });
          </script>


</head>

<body>
<?php
// $snmp_values = snmpwalk("94.56.170.196", "aazfdl", null);
// print_r($snmp_values);
?>


<div id="top">
    <div class="wrapper">
        <a href="index.php" title="" class="logo"><img src="images/fdl-logo2.png" alt="" /></a> BETA 1.0
        <!--fdl-logo2.png-->
        
        <!-- Right top nav -->
        <div class="topNav">
            <ul class="userNav">
                <li><a          title="" class="search"></a></li>
                <li><a href="#" title="" class="screen"></a></li>
                <li><a href="#" title="" class="settings"></a></li>
                <li><a href="#" title="" class="logout"></a></li>
                <li class="showTabletP"><a href="#" title="" class="sidebar"></a></li>
            </ul>
            <a title="" class="iButton"></a>
            <a title="" class="iTop"></a>
            <div class="topSearch">
                <div class="topDropArrow"></div>
                <form action="">
                    <input type="text" placeholder="search..." name="topSearch" />
                    <input type="submit" value="" />
                </form>
            </div>
        </div>
        
        <!-- Responsive nav -->
        <ul class="altMenu">
            <li><a href="index.php" title="">Dashboard</a></li>
            <li><a href="messages.html" title="">Errors</a></li>
            <li><a href="statistics.html" title="">Statistics</a></li>
            <li><a href="other_calendar.html" title="" class="exp">Other pages</a>
                <ul>
                    <li><a href="other_calendar.html">Calendar</a></li>
                    <li><a href="other_gallery.html">Images gallery</a></li>
                    <li><a href="other_file_manager.html">File manager</a></li>
                    <li><a href="other_404.html">Sample error page</a></li>
                    <li><a href="other_typography.html">Typography</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- Top line ends -->


<!-- Sidebar begins -->
<div id="sidebar">
        	        <?php include('/var/www/html/includes/mainnav.php'); 
        	        ?>
    
    <!-- Secondary nav -->
    <div class="secNav">
        <div class="secWrapper">
            <div class="secTop">
                <div class="balance">
                	<?php
                	$trueTime = date("d M y, H:i");
                	?>                	
                    <div class="balInfo">Live Clients:<span><?php echo($trueTime); ?></span></div>
                    <div class="balAmount"><span>12</span><span class="balBars"><!--5,10,15,20,18,16,14,20,15,16,12,10--></span></div>
                </div>
                <a href="#" class="triangle-red"></a>
            </div>
            
            <div class="divider"><span></span></div>
            
            <div class="sideChart">
            <div style='padding:5px;'>
                    <div class='nav'>Cloudfront:&nbsp;<span></span><br />Cloudfront stats go here, via Cloudfront API ( - not updated in realtime)
                    	<ul>
                    		<li>busy regions</li>
                    		<li>total users</li>
                    		<li>monthly traffic to date</li>
                    		<li>etc</li>
                    	</ul>
                    </div>
                    <div><span>5,140</span></div>
            </div>
            </div> 
            
            <div class="divider"><span></span></div> 
                       
             <!-- Sidebar chart -->
            <div class="sideChart">
                <div class="chartS" id='placeholderA'></div>
            </div>
           
            <div class="divider"><span></span></div>
       </div> 
   </div>
</div>
<!-- Sidebar ends -->
   
    
<!-- Content begins -->
<div id="content">
    <div class="contentTop">
        <span class="pageTitle"><span class="icon-screen"></span>Streaming Dashboard</span>
        <ul class="quickStats">
            <li>
                <a href="" class="blueImg"><img src="images/icons/quickstats/plus.png" alt="" /></a>
                <div class="floatR"><strong class="blue">5489</strong><span>visits</span></div>
            </li>
            <li>
                <a href="" class="redImg"><img src="images/icons/quickstats/user.png" alt="" /></a>
                <div class="floatR"><strong class="blue">4658</strong><span>users</span></div>
            </li>
        </ul>
    </div>
    
    <!-- Breadcrumbs line -->
    <div class="breadLine">
        <div class="bc">
            <ul id="breadcrumbs" class="breadcrumbs">
                <li><a href="#">Dashboard</a></li>
                <li><a href="#">Stats</a>
                    <ul>
                        <li><a href="ui.html" title="">General elements</a></li>
                        <li><a href="ui_icons.html" title="">Icons</a></li>
                        <li><a href="ui_buttons.html" title="">Button sets</a></li>
                        <li><a href="ui_custom.html" title="">Custom elements</a></li>
                    </ul>
                </li>
                <li class="current"><a href="ui_grid.html" title="">Monitoring</a></li>
            </ul>
        </div>
        
        <div class="breadLinks">
            <ul>
                <li><a href="#" title=""><i class="icos-alert"></i><span>Alerts</span> <strong>(+58)</strong></a></li>
                <li><a href="#" title=""><i class="icos-graph"></i><span>Errors</span> <strong>(+12)</strong></a></li>
                <li class="has">
                    <a title="">
                        <i class="icos-cog3"></i>
                        <span>Debug</span>
                        <span><img src="images/elements/control/hasddArrow.png" alt="" /></span>
                    </a>
                    <ul>
                        <li><a href="#" title=""><span class="icos-help"></span>Attempt Auto-fix</a></li>
                        <li><a href="#" title=""><span class="icos-email"></span>Email Del</a></li>
                        <li><a href="#" title=""><span class="icos-electroplug"></span>PANIC BUTTON</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    
    <!-- Main content -->
    <div class="wrapper">
      	<!-- 6 + 6 -->
        <div class="fluid">

            <!-- Calendar -->
            <div class="widget grid12">

            <div class="whead"><h6>Al Ain Zoo feeds</h6>
                <div class="titleOpt">
                    <a href="#" data-toggle="dropdown">&nbsp; Administer streams &nbsp; <span class="icos-cog3"></span></a>
                    <ul class="dropdown-menu pull-right">
                            <li><a href='http://10.35.64.140/hms/'>HMS UI</a></li>
                            <li><a href='https://nprod.projectapollo2.com/admin/cameras/#/cameras/action=list/'>Camera Control</a></li>
                            <li><a href='http://rtmp-qa1.projectapollo2.com:8080/stat'>NGINX ingest stats</a></li>
                    </ul>
                    
                </div>
            </div>
            <div class="body"><div class="cameras">
            	
<?php 
 $encoder = '10.35.64.140'; 
 $rndtime = microtime(true);
 $trueTime1 = date("d M y, H");
 $trueTime2 = date("i");
 $zooTime = intval(date("H"))+4; 
 $camArray = array("white tigers","african lions","african wild dogs","standby","meerkats","african mixed pole","african mixed hut");
 $thumb = '/hms/images/94.56.170.196-preview-900';
 
 echo ("<div style='padding:2px; border:1px solid silver;'>"); 
 echo ("&nbsp;&nbsp;&nbsp;HMS: <b><a href='http://".$encoder."/hms/'>".$encoder."</a></b>&nbsp; | &nbsp;".$trueTime1.":".$trueTime2."&nbsp; | &nbsp; &nbsp;Zoo Local Time = ".$zooTime.":".$trueTime2);
 
 echo ("<div style='padding:5px;'>");

 for ($x=0; $x<=6; $x++) {
	 
  if($x==3) 
  {echo('');} // put <br /> in here if displaying 3+3
  else 
	 {
 $camPort = $x+1;
 $fullImgPath = "http://".$encoder.$thumb.$x.".png?rnd=".$rndtime."";		 
  echo ("&nbsp;<div style='display:inline-block; padding:3px; background-size: 100% 100%; background-image:url(".$fullImgPath.")' class='ui-state-default ui-corner-all'><span style='color:white;font-weight:bold;' >&nbsp;".$camArray[$x]."&nbsp;(SDI: ".$camPort.")</span>&nbsp;&nbsp;<img src='' style='vertical-align:middle' />LIVE<br /><a href='/fdl/latency.php?url=".urlencode($camArray[$x])."' style='border:none;' title='watch ".$camArray[$x]." live' class='lightbox tipN'><img src='images/trans.gif' width='220' height='140' style='border:none;'/></a></div>");
	 }
 }  

 echo ("</div></div>");
 ?>
            	</div></div>
        </div>
       

        </div>
        

        <!--<div class="widget chartWrapper" style=''>-->
        <div class="widget chartWrapper" style='visibility:hidden;display:none;'>
            <div class="whead"><h6>Charts</h6></div>
            <div class="body"><div class="chart"></div></div>
        </div>
  
        <div class="fluid" style=''>
        
            <!-- Bars chart -->
            <div class="widget grid5 chartWrapper">
                <div class="whead"><h6>Ingest Bandwidth by channel (kbit/s)</h6></div>
                <div class="body"><div class="bars" id="placeholder1"></div></div>
            </div>
            
            <!-- Auto updating chart -->
            <div class="widget grid7 chartWrapper">
                <div class="whead"><h6>Ingest Bandwidth total - realtime (mbit/s)</h6></div>
                <div class="body"><div class="updating"></div></div>
            </div>
        
        </div>
    
        <div class="fluid" style='visibility:hidden;display:none;'>
        
            <!-- Donut -->
            <div class="widget grid4 chartWrapper">
                <div class="whead"><h6>Popularity by camera</h6></div>
                <div class="body"><div class="pie" id="donut"></div></div>
            </div>
          
            <!-- Bars chart -->
            <div class="widget grid8 chartWrapper">
                <div class="whead"><h6>Horizontal bars</h6></div>
                <div class="body"><div class="bars" id="placeholder1_h"></div></div>
            </div>
            
        </div> 

        <div class="widget">
        <div class="whead"><h6>Stream breakdown</h6></div>
        <div id="dyn" class="hiddenpars">
            <a class="tOptions" title="Options"><img src="images/icons/options" alt="" /></a>
            <table cellpadding="0" cellspacing="0" border="0" class="dTable" id="dynamic">
            
            <thead>
            <tr>
            <th>Stream<span class="sorting" style="display: block;"></span></th>
            <th>Bandwidth In</th>
            <th>Video</th>
            <th>Audio</th>
            <th>Stream Info</th>
            <th>Connected</th>
            <th>Publisher</th>
            <th>Clients</th>
            </tr>
            </thead>
            <tbody>
            	
        
        	        <?php include('/var/www/html/includes/streams.php'); 
        	        ?>
        
        
            </tbody>
            </table> 
        </div>
        </div>
      
        
        <div class="fluid">
        	
          
          <!-- Media table -->
          <div class="widget check grid6"  style='visibility:hidden;display:none;'>
            <div class="whead">
                <span class="titleIcon"><input type="checkbox" id="titleCheck" name="titleCheck" /></span>
                <h6>Media table</h6>
            </div>
            <table cellpadding="0" cellspacing="0" width="100%" class="tDefault checkAll tMedia" id="checkAll">
                <thead>
                    <tr>
                        <td><img src="images/elements/other/tableArrows.png" alt="" /></td>
                        <td width="50">Image</td>
                        <td class="sortCol"><div>Description<span></span></div></td>
                        <td width="130" class="sortCol"><div>Date<span></span></div></td>
                        <td width="120">File info</td>
                        <td width="100">Actions</td>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td colspan="6">
                            <div class="itemActions">
                                <label>Apply action:</label>
                                <select class="styled">
                                    <option value="">Select action...</option>
                                    <option value="Edit">Edit</option>
                                    <option value="Delete">Delete</option>
                                    <option value="Move">Move somewhere</option>
                                </select>
                            </div>
                            <div class="tPages">
                                <ul class="pages">
                                    <li class="prev"><a href="#" title=""><span class="icon-arrow-14"></span></a></li>
                                    <li><a href="#" title="" class="active">1</a></li>
                                    <li><a href="#" title="">2</a></li>
                                    <li><a href="#" title="">3</a></li>
                                    <li><a href="#" title="">4</a></li>
                                    <li>...</li>
                                    <li><a href="#" title="">20</a></li>
                                    <li class="next"><a href="#" title=""><span class="icon-arrow-17"></span></a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                </tfoot>
                <tbody>
                    <tr>
                        <td><input type="checkbox" name="checkRow" /></td>
                        <td><a href="images/big.png" title="" class="lightbox"><img src="images/live/face3.png" alt="" /></a></td>
                        <td class="textL"><a href="#" title="">Image1 description</a></td>
                        <td>Feb 12, 2012. 12:28</td>
                        <td class="fileInfo"><span><strong>Size:</strong> 215 Kb</span><span><strong>Format:</strong> .jpg</span></td>
                        <td class="tableActs">
                            <a href="#" class="tablectrl_small bDefault tipS" title="Edit"><span class="iconb" data-icon="&#xe1db;"></span></a>
                            <a href="#" class="tablectrl_small bDefault tipS" title="Remove"><span class="iconb" data-icon="&#xe136;"></span></a>
                            <a href="#" class="tablectrl_small bDefault tipS" title="Options"><span class="iconb" data-icon="&#xe1f7;"></span></a>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="checkRow" /></td>
                        <td><a href="images/big.png" title="" class="lightbox"><img src="images/live/face7.png" alt="" /></a></td>
                        <td class="textL"><a href="#" title="">Image1 description</a></td>
                        <td>Feb 12, 2012. 12:28</td>
                        <td class="fileInfo"><span><strong>Size:</strong> 215 Kb</span><span><strong>Format:</strong> .jpg</span></td>
                        <td class="tableActs">
                            <a href="#" class="tablectrl_small bDefault tipS" title="Edit"><span class="iconb" data-icon="&#xe1db;"></span></a>
                            <a href="#" class="tablectrl_small bDefault tipS" title="Remove"><span class="iconb" data-icon="&#xe136;"></span></a>
                            <a href="#" class="tablectrl_small bDefault tipS" title="Options"><span class="iconb" data-icon="&#xe1f7;"></span></a>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="checkRow" /></td>
                        <td><a href="images/big.png" title="" class="lightbox"><img src="images/live/face6.png" alt="" /></a></td>
                        <td class="textL"><a href="#" title="">Image1 description</a></td>
                        <td>Feb 12, 2012. 12:28</td>
                        <td class="fileInfo"><span><strong>Size:</strong> 215 Kb</span><span><strong>Format:</strong> .jpg</span></td>
                        <td class="tableActs">
                            <a href="#" class="tablectrl_small bDefault tipS" title="Edit"><span class="iconb" data-icon="&#xe1db;"></span></a>
                            <a href="#" class="tablectrl_small bDefault tipS" title="Remove"><span class="iconb" data-icon="&#xe136;"></span></a>
                            <a href="#" class="tablectrl_small bDefault tipS" title="Options"><span class="iconb" data-icon="&#xe1f7;"></span></a>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="checkRow" /></td>
                        <td><a href="images/big.png" title="" class="lightbox"><img src="images/live/face5.png" alt="" /></a></td>
                        <td class="textL"><a href="#" title="">Image1 description</a></td>
                        <td>Feb 12, 2012. 12:28</td>
                        <td class="fileInfo"><span><strong>Size:</strong> 215 Kb</span><span><strong>Format:</strong> .jpg</span></td>
                        <td class="tableActs">
                            <a href="#" class="tablectrl_small bDefault tipS" title="Edit"><span class="iconb" data-icon="&#xe1db;"></span></a>
                            <a href="#" class="tablectrl_small bDefault tipS" title="Remove"><span class="iconb" data-icon="&#xe136;"></span></a>
                            <a href="#" class="tablectrl_small bDefault tipS" title="Options"><span class="iconb" data-icon="&#xe1f7;"></span></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
            
        </div>
        
        
        
    </div>
    <!-- Main content ends -->
    
</div>
<!-- Content ends -->

</body>
</html>
