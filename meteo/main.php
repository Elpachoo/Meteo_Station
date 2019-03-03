<?php

    $dht11 = array (
        "temperature"  => array(),
        "humidity" => array()
    );



    // information pour recupere les donnes sur la page sigfox
    $username = "5954e57250057463d741a09e";
    $password = "cade1bf9b1c3f046dc0b862c364f216f";

    $homepage = file_get_contents("https://".$username.":".$password."@backend.sigfox.com/api/devices/2142D4/messages");
    $obj = json_decode($homepage);
    //print_r($homepage);
    $data = (array)$obj->{'data'}[0];
    $arg = $data['data'];
    $lenDataReceive = count((array)$obj->{'data'});
    $lenData = strlen($data['data']);

    $i = 0;
    while ($i < $lenDataReceive){
        $data = (array)$obj->{'data'}[$i];
        $arg = $data['data'];
        //print_r($arg);
        if(strlen($arg)==6){
            if($arg[0]==1){
                array_push($dht11["temperature"],-floatval($arg[1].$arg[2].".".$arg[3]));
                array_push($dht11["humidity"],intval($arg[4].$arg[5]));
            }
            else if($arg[0]==0){
                array_push($dht11["temperature"],floatval($arg[1].$arg[2].".".$arg[3]));
                array_push($dht11["humidity"],intval($arg[4].$arg[5]));
            }
        }
        $i++;
    }
?>

<?php // content="text/plain; charset=utf-8"
    require_once ('jpgraph/jpgraph.php');
    require_once ('jpgraph/jpgraph_line.php');

    $datay1 = array_reverse($dht11["temperature"]);
    // Setup the graph
    $graph = new Graph(1500,500);
    $graph->SetScale("textlin");

    $theme_class=new UniversalTheme;

    $graph->SetTheme($theme_class);
    $graph->img->SetAntiAliasing(false);
    $graph->title->Set('Temperature DHT11');
    $graph->SetBox(false);

    $graph->SetMargin(40,20,36,63);

    $graph->img->SetAntiAliasing();

    $graph->yaxis->HideZeroLabel();
    $graph->yaxis->HideLine(false); // Ligne des y
    $graph->yaxis->HideTicks(false,false); // Petits traits,grands traits

    //$graph->xgrid->Show();
    $graph->xgrid->SetLineStyle("solid");
    $graph->xgrid->SetColor('#E3E3E3');
    $graph->xaxis->HideLabels(false);
    $graph->xaxis->SetLabelAngle(90);

    // Create the first line
    $p1 = new LinePlot($datay1);
    $graph->Add($p1);
    $p1->SetColor("#6495ED");
    $p1->SetLegend('Températures');

    $graph->legend->SetFrameWeight(1);

    /*$caption=new Text("Figure 1. Temperature over time",750,250);
    $graph->AddText($caption); */

    // Output line
    if(file_exists("images/temperature.png")){
        unlink("images/temperature.png");
    }
    $graph->Stroke("images/temperature.png");
?>

<?php
    $datay1 = array_reverse($dht11["humidity"]);
    // Setup the graph
    $graph = new Graph(1500,500);
    $graph->SetScale("textlin");

    $theme_class=new UniversalTheme;

    $graph->SetTheme($theme_class);
    $graph->img->SetAntiAliasing(false);
    $graph->title->Set('Humidity DHT11');
    $graph->SetBox(false);

    $graph->SetMargin(40,20,36,63);

    $graph->img->SetAntiAliasing();

    $graph->yaxis->HideZeroLabel();
    $graph->yaxis->HideLine(false); // Ligne des y
    $graph->yaxis->HideTicks(false,false); // Petits traits,grands traits

    //$graph->xgrid->Show();
    $graph->xgrid->SetLineStyle("solid");
    $graph->xgrid->SetColor('#E3E3E3');
    $graph->xaxis->HideLabels(false);
    $graph->xaxis->SetLabelAngle(90);

    // Create the first line
    $p1 = new LinePlot($datay1);
    $graph->Add($p1);
    $p1->SetColor("#6495ED");
    $p1->SetLegend('Humidity');

    $graph->legend->SetFrameWeight(1);

    /*$caption=new Text("Figure 1. Temperature over time",750,250);
    $graph->AddText($caption); */

    // Output line
    if(file_exists("images/humidity.png")){
        unlink("images/humidity.png");
    }
    $graph->Stroke("images/humidity.png");
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="css.css" />
        <title>Weather</title>
        <script src="https://unpkg.com/vue/dist/vue.js"></script>
        <script src="https://cdn.jsdelivr.net/vue.resource/1.0.3/vue-resource.min.js"></script>
    </head>
    
    <header>
    	<?php
        $date = date("d/m/Y");
        Print("Météo du $date");
        ?>
   	</header>

    <body>
        <div id="weather">
        	<div id="bloc_1">
        		<img src="images/Lille.jpg" alt="Logo Lille" />
        		<h1> {{ lille.name }} </h1>
                <h5>{{ lille.weather[0].description }}</h5>
                <h2>T°: {{ lille.main.temp }} °C</h2>
                <h3>Humidity : {{ lille.main.humidity }} %</h3>
                <h4>Pressure : {{ lille.main.pressure }} hPa</h4>
        	</div>
        	<div id="bloc_2">
        		<img src="images/NY.jpg" alt="Logo Toronto" />
                <h1> {{ toronto.name }} </h1>
                <h5>{{ toronto.weather[0].description }}</h5>
                <h2>T° : {{ toronto.main.temp }} °C</h2>
                <h3>Humidity : {{ toronto.main.humidity }} %</h3>
                <h4>Pressure : {{ toronto.main.pressure }} hPa</h4>
       		</div>
            <div id="bloc_3">
                <img src="images/Lille.jpg" alt="Logo Paris" />
                <h1> {{ paris.name }} </h1>
                <h5>{{ paris.weather[0].description }}</h5>
                <h2>T° : {{ paris.main.temp }} °C</h2>
                <h3>Humidity : {{ paris.main.humidity }} %</h3>
                <h4>Pressure : {{ paris.main.pressure }} hPa</h4>
            </div>
            <div id="bloc_4">
                <img src="images/NY.jpg" alt="Logo NY" />
                <h1> {{ newYork.name }} </h1>
                <h5>{{ newYork.weather[0].description }}</h5>
                <h2>T° : {{ newYork.main.temp }} °C</h2>
                <h3>Humidity : {{ newYork.main.humidity }} %</h3>
                <h4>Pressure : {{ newYork.main.pressure }} hPa</h4>
            </div>  
            <div id="bloc_5">
                <h1> DHT </h1>
                <h2>T° : <?php echo $dht11["temperature"][0]; ?> °C</h2>
                <h3>Humidity : <?php echo $dht11["humidity"][0]; ?> %</h3>
                <h4><a href="temperature.html">graph temperature</a></h4>
                <h4><a href="humidity.html">graph humidity</a></h4>
            </div>
        </div>
    </body>

    <script type="text/javascript">
        var weather = new Vue({
        el: '#weather',

        data: {
            lille: [],
            toronto: [],
            paris: [],
            newYork: []
        },

        mounted: function () {
            this.getDataWeather();
        },        

        methods: {
            getDataWeather: function () {
                this.$http.get('https://api.openweathermap.org/data/2.5/weather?&APPID=c4fb8df85ad9a27885a5c5f88fa9bd55&q=Lille&units=metric') // lon=3.066667&lat=50.633333&APPID=c4fb8df85ad9a27885a5c5f88fa9bd55&units=metric&lang=fr
                          .then(response => {
                             this.lille = response.data
                          });
                this.$http.get('https://api.openweathermap.org/data/2.5/weather?&APPID=c4fb8df85ad9a27885a5c5f88fa9bd55&q=Toronto&units=metric') // lon=3.066667&lat=50.633333&APPID=c4fb8df85ad9a27885a5c5f88fa9bd55&units=metric&lang=fr
                          .then(response => {
                             this.toronto = response.data
                          });
                this.$http.get('https://api.openweathermap.org/data/2.5/weather?&APPID=c4fb8df85ad9a27885a5c5f88fa9bd55&q=Paris&units=metric') // lon=3.066667&lat=50.633333&APPID=c4fb8df85ad9a27885a5c5f88fa9bd55&units=metric&lang=fr
                          .then(response => {
                             this.paris = response.data
                          });
                this.$http.get('https://api.openweathermap.org/data/2.5/weather?&APPID=c4fb8df85ad9a27885a5c5f88fa9bd55&q=New York&units=metric') // lon=3.066667&lat=50.633333&APPID=c4fb8df85ad9a27885a5c5f88fa9bd55&units=metric&lang=fr
                          .then(response => {
                             this.newYork = response.data
                          });
            }
        }

    })
    ;
    </script>
</html>
