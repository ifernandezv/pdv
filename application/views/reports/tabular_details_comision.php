<?php
setlocale(LC_TIME, 'spanish');
include 'numerosALetras.class.php';
$V = new EnLetras();

if($export_excel == 1)
{
  if (!$this->config->item('legacy_detailed_report_export'))
  {
    $rows = array();
  
    $row = array();
    foreach ($headers['details'] as $header) 
    {
      $row[] = strip_tags($header['data']);
    }

    foreach ($headers['summary'] as $header) 
    {
      $row[] = strip_tags($header['data']);
    }
    $rows[] = $row;
  
    foreach ($summary_data as $key=>$datarow) 
    {    
      foreach($details_data[$key] as $datarow2)
      {
        $row = array();
        foreach($datarow2 as $cell)
        {
          $row[] = str_replace('&#8209;', '-', strip_tags($cell['data']));        
        }
      
        foreach($datarow as $cell)
        {
          $row[] = str_replace('&#8209;', '-', strip_tags($cell['data']));
        }
        $rows[] = $row;
      }
    
    }
  }
  else
  {
    $rows = array();
    $row = array();
    foreach ($headers['summary'] as $header) 
    {
      $row[] = strip_tags($header['data']);
    }
    $rows[] = $row;
  
    foreach ($summary_data as $key=>$datarow) 
    {
      $row = array();
      foreach($datarow as $cell)
      {
        $row[] = str_replace('&#8209;', '-', strip_tags($cell['data']));
      
      }
    
      $rows[] = $row;

      $row = array();
      foreach ($headers['details'] as $header) 
      {
        $row[] = strip_tags($header['data']);
      }
    
      $rows[] = $row;
    
      foreach($details_data[$key] as $datarow2)
      {
        $row = array();
        foreach($datarow2 as $cell)
        {
          $row[] = str_replace('&#8209;', '-', strip_tags($cell['data']));        
        }
        $rows[] = $row;
      }
    }
  }
  $content = array_to_spreadsheet($rows);
  force_download(strip_tags($title) . '.'.($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), $content);
  exit;
}

?>
<?php $this->load->view("partial/header"); ?>
<div id="content-header" class="hidden-print">
  <h1 > <i class="fa fa-bar-chart"> </i><?php echo lang('reports_reports'); ?> - <?php echo $title ?>  </h1>
</div>

<div id="breadcrumb" class="hidden-print">
  <?php echo create_breadcrumb(); ?>
</div>
<div class="clear hidden-print" ></div>
  <div class="row hidden-print">
                <div class="center" style="text-align: center;">          
                    <ul class="stat-boxes">
                        <?php foreach($overall_summary_data as $name=>$value) { ?>
                        <li class="popover-visits">
                            <div class="left peity_bar_good"><h5><?php echo lang('reports_'.$name); ?></h5></div>
                            <div class="right">
                                <strong><?php echo str_replace(' ','&nbsp;', to_currency($value)); ?></strong>
                            </div>
                        </li>
                        <?php }?>
                        
                    </ul>
                </div>  
  </div>
  
  <?php if(isset($pagination) && $pagination) {  ?>
    <div class="pagination hidden-print alternate text-center fg-toolbar ui-toolbar" id="pagination_top" >
      <?php echo $pagination;?>
    </div>
  <?php }  ?>
<div id="recibo">  
  <div id="content-header">
    <div style="float:left;"> <h1> <i class="fa fa-bar-chart"> </i>RECIBO </h1></div>
         <?php if($this->config->item('company_logo')) {?>
      <div style="float:right;"><?php echo img(array('src' => $this->Appconfig->get_logo_image())); ?></div>
    <?php } ?>
  </div>
    <div class="row" >
    <div class="">
      <div class="widget-box">
        <div class="widget-title">

                <h5>Comisiones  <?php echo $subtitle ?></h5>
                </div>
                  <div class="widget-content nopadding">
            <div class="table-responsive">
                                          <div style=" float: right">
                                            <ul class="stat-boxes">
                                               <?php foreach($overall_summary_data as $name=>$value) { ?>
                                               <?php if ($name=='commission'){ ?> 
                                                <li class="popover-visits">
                                                    <div class="left peity_bar_good"><h5><?php echo lang('reports_'.$name); ?></h5></div>
                                                    <div class="right">
                                                        <strong><?php echo str_replace(' ','&nbsp;', to_currency($value)); ?></strong>
                                                    </div>
                                                </li>
                                                <?php }?>
                                                <br>
                                               <?php }?> 
                                                                                           <?php foreach($overall_summary_data as $name=>$value) { ?>
                                               <?php if ($name=='total'){ ?> 
                                                <li class="popover-visits">
                                                    <div class="left peity_bar_good"><h5><?php echo lang('reports_'.$name); ?></h5></div>
                                                    <div class="right">
                                                        <strong><?php echo str_replace(' ','&nbsp;', to_currency($value)); ?></strong>
                                                    </div>
                                                </li>
                                                <?php }?>
                                               <?php }?>   
                                               
                                          </ul>
                                        </div>
                      <div class="left peity_bar_good" style="padding:20px; font-size:16px">
                   
                     <div > Yo <b><?php echo $title?> </b>, recibí <br></div>
                     <div > La suma de:  <?php echo strtoupper($V->ValorEnLetras($value,""));?> Bs. <br> </div>
                     <div > Por concepto de <b>"PAGO DE COMISIONES"</b> de la fecha <?php echo $fecha_inicio?> al <?php echo $fecha_fin?><br> </div>
                     
                  
                    
                      
                       <div class="center" style="text-align: center;">     
                         <ul class="stat-boxes">
                                               <li class="popover-visits">
                                                    <div class="left peity_bar_good"><h5>Recibi Conforme</h5></div>
                                                   <div class="right" style="width:100px!important">
                                                        <strong></strong>
                                                    </div>
                                                </li>
                                                     <li class="popover-visits">
                                                    <div class="left peity_bar_good"><h5>Entregue Conforme</h5></div>
                                                    <div class="right" style="width:100px!important">
                                                        <strong></strong>
                                                    </div>
                                                </li>

                                          </ul>
                           </div>   
                             <div > <?php echo $this->Location->get_info_for_key('name').",". strftime('&nbsp;  %A %#d de %B del %Y') ?></div>    
                        </div>             
                                 
                    </div>
                                   
               </div>   
            </div>
         </div>
     </div>
 </div>
  
<div class="row hidden-print" >
    <div class="col-md-12">
      <div class="widget-box">
        <div class="widget-title">
          <h5><?php echo $subtitle ?></h5>
        </div>
<div class="widget-content nopadding">
          <div class="table-responsive">
          <table class="table table-bordered table-striped  table-condensed table-hover detailed-reports  tablesorter" id="sortable_table">
            <thead>
              <tr align="center" style="font-weight:bold">
                <td class="hidden-print"><a href="#" class="expand_all" >+</a></td>
                <?php foreach ($headers['summary'] as $header) { ?>
                <td align="<?php echo $header['align']; ?>"><?php echo $header['data']; ?></td>
                <?php } ?>
              
              </tr>
            </thead>
            <tbody>
              <?php foreach ($summary_data as $key=>$row) { ?>
              <tr>
                <td class="hidden-print"><a href="#" class="expand" style="font-weight: bold;">+</a></td>
                <?php foreach ($row as $cell) { ?>
                <td align="<?php echo $cell['align']; ?>"><?php echo $cell['data']; ?></td>
                <?php } ?>
              </tr>
              <tr>
                <td colspan="<?php echo count($headers['summary']) + 1; ?>" class="innertable">
                  <table class="table ">
                    <thead>
                      <tr>
                        <?php foreach ($headers['details'] as $header) { ?>
                        <th align="<?php echo $header['align']; ?>"><?php echo $header['data']; ?></th>
                        <?php } ?>
                      </tr>
                    </thead>

                    <tbody>
                      <?php foreach ($details_data[$key] as $row2) { ?>

                        <tr>
                          <?php foreach ($row2 as $cell) { ?>
                          <td align="<?php echo $cell['align']; ?>"><?php echo $cell['data']; ?></td>
                          <?php } ?>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <?php if(isset($pagination) && $pagination) {  ?>
    <div class="pagination hidden-print alternate text-center fg-toolbar ui-toolbar" id="pagination_top" >
      <?php echo $pagination;?>
    </div>
  <?php }  ?>
<?php $this->load->view("partial/footer"); ?>
<script type="text/javascript" language="javascript">
$(document).ready(function()
{
  $(".tablesorter a.expand").click(function(event)
  {
    $(event.target).parent().parent().next().find('td.innertable').toggle();
    
    if ($(event.target).text() == '+')
    {
      $(event.target).text('-');
    }
    else
    {
      $(event.target).text('+');
    }
    return false;
  });
  
  $(".tablesorter a.expand_all").click(function(event)
  {
    $('td.innertable').toggle();
    
    if ($(event.target).text() == '+')
    {
      $(event.target).text('-');
      $(".tablesorter a.expand").text('-');
    }
    else
    {
      $(event.target).text('+');
      $(".tablesorter a.expand").text('+');
    }
    return false;
  });
  
});
</script>