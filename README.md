# HXL Chart API

Generates a JS script that shows a spark line chart, using  [Highcharts JS](http://www.highcharts.com).

# Quickstart

Call the script with an **emergency** argument, providing the URI of the emergency (required). The script a JS script that fills a DIV (id="container") on the page embedding this script with a Highchart. The chart displays the person counts for all refugee populations related to that emergency over time.

If you don't know the URI for your emergency, please consult [this list of emergencies](http://sparql.carsten.io/?query=prefix%20hxl%3A%20%3Chttp%3A//hxl.humanitarianresponse.info/ns/%23%3E%0A%0ASELECT%20*%20WHERE%20%7B%0A%20%20%3Femergency%20a%20hxl%3AEmergency%20%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20hxl%3AcommonTitle%20%3Ftitle%20.%0A%7D&endpoint=http%3A//hxl.humanitarianresponse.info/sparql) we currently have in HXL. 

# Sample Queries

We have an instance of this script running at [http://hxl.humanitarianresponse.info/api/mali-chart.htm](http://hxl.humanitarianresponse.info/api/mali-chart.htm): The page shows a chart with the refugee counts for the Mali crisis (based on our test data) over time.