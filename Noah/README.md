# Projet-kart-Dossier-Noah<br>

c'est un projet d'instrumentation d'un kart afin de récolter des données et de les afficher sur un site web
<br>

## C'est un dossier que pour Noah <br>

Vous pouvez faire ce que vous voulez c'est votre espace
<br>
<br>
Code de la boucle de courant : 
<br>
int HassPin = A0;
int HassState = 0;

void setup() {
  pinMode(HassPin, INPUT); // Set A0 as input
  Serial.begin(9600);      // Initialize serial communication
}

void loop() {
  HassState = digitalRead(HassPin); // Read the state of A0
  Serial.println(HassState);        // Print the state
  delay(1000);                      // Optional: delay for readability
}

<br>
<br>


## Lien

PHP my Admin et MySQL sur rasberry :<br>

https://medium.com/@gaudiondev/mysql-server-phpmyadmin-raspberry-pi-lamp-server-bcce4f88b34b
