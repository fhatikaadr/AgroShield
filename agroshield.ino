// --- Library yang Dibutuhkan ---
#include <WiFi.h>
#include <HTTPClient.h>
#include <ESP32Servo.h>
#include "DHT.h"

// --- Konfigurasi Jaringan ---
const char* ssid = "Brie's Phone";      // NAMA WIFI 
const char* password = "aaAAaaAA";    // PASSWORD WIFI 
const char* serverName = "http://192.168.233.217:8080/api/sensor"; // IP KOMPUTER 

// --- PIN DEFINISI ---
const int rainSensorPin = 34;  // Pin sensor hujan
const int ldrPin = 35;         // Pin sensor cahaya (LDR)
#define DHTPIN 5               // Pin DATA sensor DHT22
#define DHTTYPE DHT22

Servo myServo;
int servoPin = 26;

DHT dht(DHTPIN, DHTTYPE);

// --- PENGATURAN AMBANG BATAS (THRESHOLD) SENSOR ---
// PENTING: Kalibrasi nilai ini dengan melihat output di Serial Monitor!
const int rainThreshold = 1000;
const int lightVeryBrightThreshold = 500;
const float tempVeryHotThreshold = 35.0;

// --- PENGATURAN POSISI SERVO ---
const int posisiTutup = 0;
const int posisiBuka = 90;

// --- PENGATURAN WAKTU & DEBOUNCE ---
const unsigned long openDelay = 5000;       // Jeda 5 detik setelah hujan berhenti
const unsigned long debounceDelay = 1000;

// --- VARIABEL GLOBAL ---
bool rainDetected = false;
bool lightDetected = false; // Variabel ini tetap ada untuk dikirim ke web, tapi tidak untuk kontrol servo
bool atapTertutup = false;
unsigned long waktuHujanBerhenti = 0;
int currentServoPos = posisiBuka;

// --- FUNGSI UNTUK MENGGERAKKAN SERVO DENGAN HALUS ---
void moveServoSmooth(int fromPos, int toPos) {
  Serial.print("Menggerakkan servo dari ");
  Serial.print(fromPos);
  Serial.print(" ke ");
  Serial.println(toPos);
  
  int step = (fromPos < toPos) ? 1 : -1;
  
  for (int pos = fromPos; pos != toPos; pos += step) {
    myServo.write(pos);
    delay(15);
  }
  
  myServo.write(toPos);
  currentServoPos = toPos;
}

void setup() {
  Serial.begin(115200);
  dht.begin();
  myServo.attach(servoPin);
  myServo.write(posisiBuka);
  
  Serial.print("Menghubungkan ke WiFi: ");
  Serial.println(ssid);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi Terhubung!");
  Serial.print("Alamat IP ESP32: ");
  Serial.println(WiFi.localIP());
  
  Serial.println("Sistem AgroShield dimulai...");
}

void loop() {
  // === BACA SEMUA NILAI SENSOR ===
  int rainValue = analogRead(rainSensorPin);
  int ldrValue = analogRead(ldrPin);
  float temperature = dht.readTemperature();
  float humidity = dht.readHumidity();

  if (isnan(temperature) || isnan(humidity)) {
    Serial.println("Gagal membaca sensor DHT!");
    delay(2000);
    return;
  }

  // Cetak nilai untuk debugging
  Serial.print("Rain ADC: "); Serial.print(rainValue);
  Serial.print(" | LDR ADC: "); Serial.print(ldrValue);
  Serial.print(" | Temp: "); Serial.print(temperature);
  Serial.print("°C | Humidity: "); Serial.print(humidity);
  Serial.println("%");

  // === LOGIKA DETEKSI KONDISI (HUJAN & CERAH) ===
  rainDetected = (rainValue < rainThreshold);
  lightDetected = (ldrValue < lightVeryBrightThreshold);

  // ==========================================================
  // === LOGIKA KONTROL ATAP (SERVO) - TELAH DIPERBARUI ===
  // ==========================================================
  // Kondisi 1: Jika terdeteksi hujan
  if (rainDetected) {
    if (!atapTertutup) {
      Serial.println("HUJAN TERDETEKSI! Menutup atap...");
      moveServoSmooth(currentServoPos, posisiTutup);
      atapTertutup = true;
    }
    waktuHujanBerhenti = 0; // Reset timer karena masih hujan
  } 
  // Kondisi 2: Jika TIDAK hujan
  else {
    // Dan jika atap saat ini sedang tertutup
    if (atapTertutup) {
      // Mulai timer jika baru pertama kali terdeteksi tidak hujan
      if (waktuHujanBerhenti == 0) {
        waktuHujanBerhenti = millis();
        Serial.println("Hujan berhenti. Memulai timer untuk membuka atap...");
      }

      // PERUBAHAN: Kondisi pengecekan cahaya (&& lightDetected) DIHAPUS.
      // Atap akan terbuka setelah jeda 5 detik, tidak peduli kondisi cahaya.
      if (millis() - waktuHujanBerhenti >= openDelay) {
        Serial.println("Timer selesai. Membuka atap...");
        moveServoSmooth(currentServoPos, posisiBuka);
        atapTertutup = false;
        waktuHujanBerhenti = 0; // Reset timer setelah atap terbuka
      }
    }
  }
  // ==========================================================
  // === AKHIR DARI LOGIKA KONTROL ATAP ===
  // ==========================================================


  // === PENGIRIMAN DATA KE SERVER WEB ===
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    String postData = "suhu=" + String(temperature) +
                      "&kelembapan=" + String(humidity) +
                      "&cahaya=" + String(ldrValue) +
                      "&status_hujan=" + String(rainDetected ? 1 : 0);

    http.begin(serverName);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    int httpResponseCode = http.POST(postData);

    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println("HTTP Response code: " + String(httpResponseCode));
      Serial.println("Respon server: " + response);
    } else {
      Serial.print("Gagal mengirim data, error code: ");
      Serial.println(httpResponseCode);
    }
    
    http.end();
  } else {
    Serial.println("Koneksi WiFi terputus.");
  }

  Serial.print("Status Atap Saat Ini: ");
  Serial.println(atapTertutup ? "TERTUTUP" : "TERBUKA");
  Serial.println("-------------------------------------");

  delay(5000); // Jeda 5 detik sebelum loop berikutnya
}