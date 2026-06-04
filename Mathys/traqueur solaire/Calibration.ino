#include <Arduino.h>

// ===== LDR =====
const int LDR_HAUT = A0;
const int LDR_BAS = A1;
const int LDR_DROITE = A2;
const int LDR_GAUCHE = A3;

const int NOMBRE_LECTURES = 5;

// ===== Fonction de lissage =====
int lectureLisse(int pin) {
  long total = 0;
  for (int i = 0; i < NOMBRE_LECTURES; i++) {
    total += analogRead(pin);
  }
  return total / NOMBRE_LECTURES;
}

void setup() {
  Serial.begin(115200);
  Serial.println("=== MODE CALIBRATION ===");
  Serial.println("Place la lumière bien centrée...");
  delay(3000);
}

void loop() {

  int haut = lectureLisse(LDR_HAUT);
  int bas = lectureLisse(LDR_BAS);
  int droite = lectureLisse(LDR_DROITE);
  int gauche = lectureLisse(LDR_GAUCHE);

  // Valeur de référence = la plus élevée
  int reference = max(max(haut, bas), max(droite, gauche));

  // Calcul des facteurs
  float facteurHaut = float(reference) / haut;
  float facteurBas = float(reference) / bas;
  float facteurDroite = float(reference) / droite;
  float facteurGauche = float(reference) / gauche;

  Serial.println("----- Valeurs brutes -----");
  Serial.print("Haut: "); Serial.println(haut);
  Serial.print("Bas: "); Serial.println(bas);
  Serial.print("Droite: "); Serial.println(droite);
  Serial.print("Gauche: "); Serial.println(gauche);

  Serial.println("----- Facteurs calculés -----");
  Serial.print("facteurHaut = "); Serial.println(facteurHaut, 4);
  Serial.print("facteurBas = "); Serial.println(facteurBas, 4);
  Serial.print("facteurDroite = "); Serial.println(facteurDroite, 4);
  Serial.print("facteurGauche = "); Serial.println(facteurGauche, 4);

  Serial.println("------------------------------\n");

  delay(2000);
}