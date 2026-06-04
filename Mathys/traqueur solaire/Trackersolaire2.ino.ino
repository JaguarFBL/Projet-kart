#include <Arduino.h>
#include <Servo.h>

// ===== Servos =====
Servo servoVertical;
Servo servoHorizontal;

// ===== Limites mécaniques =====
const int LIMITE_MIN_V = 60;
const int LIMITE_MAX_V = 135;
float positionVerticale = (LIMITE_MIN_V + LIMITE_MAX_V) / 2;

const int LIMITE_MIN_H = 10;
const int LIMITE_MAX_H = 170;
float positionHorizontale = (LIMITE_MIN_H + LIMITE_MAX_H) / 2;

// ===== LDR =====
const int CAPTEUR_HAUT = A2;
const int CAPTEUR_BAS = A1;
const int CAPTEUR_DROITE = A0;
const int CAPTEUR_GAUCHE = A3;

const int NOMBRE_LECTURES = 3;

// ===== Facteurs fixes =====
const float facteurHaut = 1.0415;
const float facteurBas = 1.0281;
const float facteurDroite = 1.0000;
const float facteurGauche = 1.0115;

// ===== Smooth tracking =====
const float facteurFluidite = 0.10;

// ===== Fonction de lissage =====
int lectureLisse(int pin) {
  long total = 0;
  for (int i = 0; i < NOMBRE_LECTURES; i++) {
    total += analogRead(pin);
  }
  return total / NOMBRE_LECTURES;
}

// ===== Boucle tracker =====
void boucleSuivi() {

  int haut    = lectureLisse(CAPTEUR_HAUT) * facteurHaut;
  int bas     = lectureLisse(CAPTEUR_BAS) * facteurBas;
  int droite  = lectureLisse(CAPTEUR_DROITE) * facteurDroite;
  int gauche  = lectureLisse(CAPTEUR_GAUCHE) * facteurGauche;

  int deltaV = haut - bas;
  int deltaH = gauche - droite;

  float cibleVerticale = positionVerticale - deltaV * 0.05;
  float cibleHorizontale = positionHorizontale + deltaH * 0.05;

  // Mouvement fluide
  positionVerticale   += (cibleVerticale - positionVerticale) * facteurFluidite;
  positionHorizontale += (cibleHorizontale - positionHorizontale) * facteurFluidite;

  // Limites mécaniques
  positionVerticale   = constrain(positionVerticale, LIMITE_MIN_V, LIMITE_MAX_V);
  positionHorizontale = constrain(positionHorizontale, LIMITE_MIN_H, LIMITE_MAX_H);

  servoVertical.write(positionVerticale);
  servoHorizontal.write(positionHorizontale);
}

void setup() {
  servoVertical.attach(10);
  servoHorizontal.attach(9);

  servoVertical.write(positionVerticale);
  servoHorizontal.write(positionHorizontale);
}

void loop() {
  boucleSuivi();
  delay(10);
}