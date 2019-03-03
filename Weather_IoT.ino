#include <ESP8266WiFi.h>
#include <ArduinoJson.h>
#include <SoftwareSerial.h>
#include "DHT.h"

#define DHTPIN D2     // what digital pin we're connected to
#define DHTTYPE DHT11   // DHT 22  (AM2302), AM2321


#define RxNodePin 13
#define TxNodePin 15

#define LENGTH_MESSAGE 6

// Setup UART Communication with 
SoftwareSerial Sigfox =  SoftwareSerial(RxNodePin, TxNodePin);

DHT dht(DHTPIN, DHTTYPE);

const char* ssid = "YOUR_SSID";
const char* password = "YOUR_PASSWORD";

void setup() 
{
  Serial.begin(115200);
  
  pinMode(RxNodePin, INPUT);
  pinMode(TxNodePin, OUTPUT);
  Sigfox.begin(9600);
  delay(100);
  dht.begin();
  
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) 
  {
    delay(1000);
    Serial.println("Connecting...");
  }
}

unsigned int count;
String message1 = "";
String signe;

void loop() 
{
   message1 = getMessageToSend();
   if(message1 != "") {
    Serial.println(sendMessage(message1));
    delay(600000);
   }
}



String getMessageToSend(){
    String message = "";
    
    // Reading temperature & humidity
    float hum = dht.readHumidity();
    // Read temperature as Celsius (the default)
    float temp = dht.readTemperature();
    // Check if any reads failed and exit early (to try again).
    if (isnan(hum) || isnan(temp)) {
      return "";
    }
    if(temp<0) signe="1";
    else signe="0";
    int temperature = int(temp*10);
    int humidity = int(hum);
    //Serial.println("temperature : " + String(temperature));
    //Serial.println("Hum : " + String(humidity));
    message = String(signe) + String(int(temperature/100))+String(int(temperature/10)%10)+String(int(temperature%10))+String(int(humidity/10))+String(int(humidity%10));

    
    //Serial.println(message);
    return message;
}



String sendMessage(String message) {
  String status = "";
  char sigfoxBuffer;

  // Send AT$SF=xx to WISOL to send XX (payload data of size 1 to 12 bytes)
  Sigfox.print("AT$SF=");
  for(int i = 0; i < LENGTH_MESSAGE; i++)
    Sigfox.print(message[i]);
  
  Sigfox.print("\r");

  while (!Sigfox.available()){
     delay(10);
  }

  while(Sigfox.available()){
    sigfoxBuffer = (char)Sigfox.read();
    status += sigfoxBuffer;
    delay(10);
  }

  return status;
}
