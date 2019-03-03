# Projet d'IoT - ISEN Lille : Création d'une station météo

### Par : Barbé Gatien, Bourdeau Martin, Dumser Clément et Ghys Robin
=====================================================================

## Présentation générale :

Dans le cadre de notre formation en **IoT**, nous avons participé à un projet de création d'une **station météo**. A partir de **l'ESP8266** ainsi que du module **SigFox BRKWS01**, nous devions faire des acquisitions de températures. Ensuite, le tout était envoyé sur l'API de SigFox depuis laquelle nous deviens récupérer ces données et les afficher sur une page web.

## Fonctionnement :

Afin de concevoir cette station météo, nous avons utilisé plusieurs technologies différentes.
Comme dit dans la présentation, nous avions une carte arduino et un module SigFox mis à disposition afin de pouvoir envoyer les températures ainsi que l'humidité relevées sur les serveurs SigFox.

L'intérêt d'utiliser SigFox réside dans le fait que nos données sont envoyées sur des serveurs. Ainsi, elles sont stockées et formatées selon un format prédéfinies. Nous n'avons ensuite qu'à appelé le fichier **.json** généré pour lire les données obtenues par le capteur.

Afin d'afficher ces données, nous avons créé une page web. En vous connectant dessus, vous pouvez remarquer qu'il n'y a pas seulement les données récupérées par le DHT qui apparaissent.
En effet, les données sur les 4 autres villes sont obtenues via **l'API OpenWeather**. OpenWeather est un site ouvert à tous permettant de récupérer des données météorologiques en fonction de leur classe. Nous avons donc décidé d'afficher le temps, la température, l'humidité, et la pression.

### Page Web :

C'est sur cette page que vous allez pouvoir visualiser nos relevés météo. 
On initialise d'abord la connexion à SigFox. Pour ce faire on a automatisé la connexion afin que l'utilisateur obtienne instantanément les relevés du **capteur DHT**.

Lorsque la connexion est faite, un fichier au format *.json* est reçu. On va ensuite transformer la partie **data** en array (par une opération de **casting**). On sélectionne ensuite l'élément **data** dans ce nouveau tableau. Cet élément correspond aux données météo envoyées par le module SigFox.

En tout, on récupère 100 valeurs depuis l'API SigFox que l'on va ensuite afficher dans un **graphique**. 

[API SigFox]: Photo_Rapport/capture_API_SigFox.png

Cependant, lors de l'envoi de ces données, le module n'est pas capable de dire si une température est positive ou négative. Nous avons donc transformé les signes **-** et **+** en chiffre. Si **arg[0] = 1**, cela signifie que la température est une température négative. En revanche, si **arg[0] = 0**, la température est positive.

```php
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
```

#### Capture de la page web

[Page web]: Photo_Rapport/capture_site.png

#### Capture des graphiques de température et d'humidité

Pour afficher les graphiques, on les enregistre au préalable en tant qu'image au format **.png**. Ensuite on appelle ces images sur des pages webs dédiées auxquelles on peut accéder depuis la page principale du site web.

```c
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<div style="text-align: center;">
            <img src="images/temperature.png" alt="Temperatures">
    </div>
    <a href="main.php"> back to main page </a>
</body>
</html>
```

```c
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<div style="text-align: center;">
            <img src="images/humidity.png" alt="Humidity">
    </div>
    <a href="main.php"> back to main page </a>
</body>
</html>
```

[Graphique de la température]: Photo_Rapport/capture_graphique_temperature
[Graphique de l'humidité]: Photo_Rapport/capture_graphique_humidite

Pour ce qui est de l'affichage des données météo des villes, comme dit précédemment, nous passons par l'API d'OpenWeather. Pour ce faire, nous utilisons donc un script **.json** intégré dans notre page web afin d'afficher les données.

Pour ce faire, on créé une nouvelle vue et on précise le type de l'information avec un *el : '#weather'*. On a ensuite créé la fonction **getDataWeather** qui va nous permettre de récupérer les données météorologiques de l'API d'OpenWeather. La réponse est ensuite stockée dansu ne variable correspondant à sa ville.

L'avantage ici est que la réponse obtenue est au format **.json**. Et étant donné que tout est déjà au format **.json**, nous n'avons aucun soucis de conflit d'information.

```java
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
```

## Installation et utilisation

Afin de pouvoir utiliser notre station météo, vous devez au préalable vous munir d'un module SigFox, d'un ESP8266. 

**!!! ATTENTION !!!** Veuillez vous assurer que le module SigFox que vous allez utiliser correspond bien à celui qui est enregistré sur SigFox et à partir duquel nous faisons l'acquisition de donnée.

Pour faire fonctionner la station météo, vous allez avoir besoin du script arduino *Weather_IoT*que vous allez téléverser dans l'ESP.

Une fois cela effectué, il ne vous reste qu'à ouvrir la page web. Pour se faire, enregistrez le dossier *meteo* dans votre dossier *www* dans **wamp**. Ouvrez ensuite une page internet et connectez-vous à votre **localhost**. Allez dans vos dossiers, ouvrez *meteo* et lancez le fichier *main.php*.

Vous pouvez maintenant observer les températures à Lille ou bien encore Toronto, mais également celles acquises par le DHT. 

Si le coeur vous en dit, vous pouvez consulter l'évolution des températures et de l'humidité mesurées par le module.