<?php
header('Content-Type: application/javascript');  
include_once('lib/sparqllib/sparqllib.php');

// we need to know the emergency:
if(!isset($_GET['emergency'])){

  echo 'Please add the emergency parameter to your query to let me know the emergency for which you want the refugee numbers.';
  die();

}

function queryRefugeesLowestLevel(){
  $query = "
prefix ogc: <http://www.opengis.net/ont/geosparql#> 
prefix hxl: <http://hxl.humanitarianresponse.info/ns/#>

SELECT DISTINCT

  ?valid
  ?unit 
  ?unitName 
 
  (MAX(?lvl) as ?level) 
  (SUM(?count) AS ?totalRefugees) 

WHERE {
  
  GRAPH ?g {
    ?g hxl:aboutEmergency <" . $_GET['emergency'] . "> ; 
       hxl:validOn ?vld .
    ?pop a hxl:RefugeesAsylumSeekers ; 
           hxl:personCount ?count ;
           hxl:atLocation  ?unit .
  }

  ?unit hxl:featureName ?unitName .

  BIND (SUBSTR(STR(?vld), 0, 11) AS ?valid ) .
} 

GROUP BY ?unit ?unitName ?valid ?wkt ?level 
ORDER BY ?unit ?valid
";

    $queryResult = getQueryResults($query);

    if ($queryResult->num_rows() == 0){
      echo 'no result';
      die();
    } else {

    $return = '';
    $lastUnit = '-';

    while( $row = $queryResult->fetch_array() ){  

        // check if we are still dealing with data from the same location:
        if($row["unit"] !== $lastUnit){
            // close the last one (if we are not at the first location):
            if($lastUnit !== '-'){
                $return = substr($return, 0, -2); // remove trailing comma to make it valid JSON
                    $return .= "]
                }, ";
            }

            $return .= "{
                name: '".$row["unitName"]."',
                data: [";

            $lastUnit = $row["unit"];
        }

        $return .= "[".formatDate($row["valid"]).", ".$row["totalRefugees"]." ], ";
    
    };

    $return = substr($return, 0, -2); // remove trailing comma to make it valid JSON
    $return .= "] } ";
    echo $return;

    }
}

// returns something like Date.UTC(1970,  9, 27)
function formatDate($dateString){
    // return "Date.UTC(".substr($dateString, 0, 4).", ".substr($dateString, 5, 2).", ".substr($dateString, 8, 2).", ".substr($dateString, 11, 2).", ".substr($dateString, 14, 2).", ".substr($dateString, 17, 2).")";    

    return "Date.UTC(".substr($dateString, 0, 4).", ".substr($dateString, 5, 2).", ".substr($dateString, 8, 2).")";    
}

function getQueryResults($query){
   try {
        $db = sparql_connect( "http://hxl.humanitarianresponse.info/sparql" );
        
        if( !$db ) {
            print $db->errno() . ": " . $db->error(). "\n"; exit;
        }
        $result = $db->query($query);
        if( !$result ) {
            print $db->errno() . ": " . $db->error(). "\n"; exit;
        }

    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  return $result;
}

?>

$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'spline'
            },
            title: {
                text: 'Mali crisis: Refugees per Location'
            },
            subtitle: {
                text: 'Data source: HXL triple store test data'
            },
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: { 
                    day: '%e. %b %Y',                    
                }
            },
            yAxis: {
                title: {
                    text: 'Number of refugees'
                },
                min: 0
            },
            tooltip: {
                formatter: function() {
                        return '<b>'+ this.series.name +'</b><br/>'+
                        Highcharts.dateFormat('%e. %b %Y', this.x) +': '+ this.y;
                }
            },
            
            series: [<?php queryRefugeesLowestLevel(); ?>]
        });
    });
    
});


