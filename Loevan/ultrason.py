from gpiozero import DistanceSensor
sensor=DistanceSensor(echo=23, trigger=24)
print(sensor)