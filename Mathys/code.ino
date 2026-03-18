#include <WiFi.h>
#include <HTTPClient.h>
 
void setup() {
  Serial.begin(115200);
  WiFi.begin("VOTRE_SSID", "VOTRE_PASSWORD");
  while (WiFi.status() != WL_CONNECTED) delay(500);
  Serial.println("Connecté!");
}
 
void loop() {
  HTTPClient http;
  http.begin("http://192.168.1.100:5000/msg");
  http.POST("Hello!");
  http.end();
  delay(5000);
}
