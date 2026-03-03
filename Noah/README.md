# Projet-kart-Dossier-Noah<br>

C'est un projet d'instrumentation d'un kart afin de récolter des données et de les afficher sur un site web
<br>

## C'est un dossier que pour Noah <br>

Vous pouvez faire ce que vous voulez c'est votre espace
<br>
<br>
Code de la boucle de courant : <br>
<br>
int HassPin = A0; <br>
int HassState = 0; <br>

void setup() { <br>
  pinMode(HassPin, INPUT); // Set A0 as input <br>
  Serial.begin(9600);      // Initialize serial communication <br>
} <br>

void loop() { <br>
  HassState = digitalRead(HassPin); // Read the state of A0 <br>
  Serial.println(HassState);        // Print the state <br>
  delay(1000);                      // Optional: delay for readability <br>
} <br>

<br>
<br>


## Lien

PHP my Admin et MySQL sur rasberry :<br>

https://medium.com/@gaudiondev/mysql-server-phpmyadmin-raspberry-pi-lamp-server-bcce4f88b34b
