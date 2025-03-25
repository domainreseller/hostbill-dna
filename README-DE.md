<div align="center">  
  <a href="README.md"   >   TR <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/TR.png" alt="TR" height="20" /></a>  
  <a href="README-EN.md"> | EN <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/US.png" alt="EN" height="20" /></a>  
  <a href="README-AZ.md"> | AZ <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/AZ.png" alt="AZ" height="20" /></a>  
  <a href="README-DE.md"> | DE <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/DE.png" alt="DE" height="20" /></a>  
  <a href="README-FR.md"> | FR <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/FR.png" alt="FR" height="20" /></a>  
  <a href="README-AR.md"> | AR <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/AR.png" alt="AR" height="20" /></a>  
  <a href="README-CN.md"> | CN <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/CN.png" alt="AR" height="20" /></a>  
  <a href="README-NL.md"> | NL <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/NL.png" alt="NL" height="20" /></a>  
</div>


# Übersicht

**DomainNameApi** ist ein führender Domain-Name-Registrar, der Domain-Name-Registrierung und andere Online-Dienste für kleine und Heimbasierte Unternehmen, Einzelpersonen, Traffic-Aggregatoren und Wiederverkäufer bietet. HostBill ermöglicht Ihnen die Automatisierung der **DomainNameApi** Domain-Bereitstellung und Verwaltung.

## Modulaktivierung
Zuerst müssen Sie Dateien in Ihr HostBill Verzeichnis hochladen:

Um das Modul zu aktivieren, loggen Sie sich in Ihr HostBill Admin Panel ein, gehen Sie zu Einstellungen → Module → Domain Module, finden und wählen Sie das **DomainNameApi** Modul und klicken Sie auf Aktivieren.

![](image.jpg)

## Modulkonfiguration

Sobald Sie das Modul aktivieren, werden Sie zur Modulkonfigurationsseite weitergeleitet. Um das aktivierte Modul zu konfigurieren, können Sie auch zu Einstellungen → Apps → Neue App hinzufügen gehen.

Füllen Sie die Konfigurationsfelder aus:

- Name der Anwendung
- Benutzername
- Passwort

Fahren Sie dann mit der Hinzufügung Ihrer Nameserver fort:

- Primärer Nameserver
- Primäre Nameserver IP

Verwenden Sie die Testkonfiguration, um zu überprüfen, ob HostBill eine Verbindung herstellen kann.

Klicken Sie auf Neue App hinzufügen.

# Domain-Import

Gehen Sie zu Extras -> Import -> Dienste importieren. Das Domainnameapi-Modul sollte in der Liste erscheinen. Wählen Sie es aus und klicken Sie auf Weiter.
![img_1.png](img_1.png)
Domainnamen werden aufgelistet. Sie können Kunden und Produkte zuordnen und importieren.

![img.png](img.png)

# Domain-Preisgestaltung

Gehen Sie zu Extras -> Import -> TLD-Preise, Erstellen Sie ein Profil.
Füllen Sie die Tarife und andere Einstellungen sorgfältig aus.

![img_2.png](img_2.png)

# Domain-Abfrageeinstellungen

Um Abfrageeinstellungen für eine Erweiterung festzulegen, gehen Sie zum Tab Einstellungen -> Domain-Einstellungen.
![img_3.png](img_3.png)
Beim Bearbeiten der Erweiterung wählen Sie Domainnameapi als Whois-Engine und Konformitätsprüfung. Auf diese Weise werden Ihre Abfragen über Domainnameapi statt über allgemeine Whois-Server durchgeführt.
Hinweis: Für diesen Vorgang müssen Sie die Datei whois.custom.php.example im Hostbill-Verzeichnis > includes > extend > whois in whois.custom.php umbenennen.

![img_4.png](img_4.png)

# Voraussetzungen für den TR-Domain-Registrierungsprozess
Gehen Sie zu Einstellungen -> Produkte -> Wählen Sie Ihr Domain-Produkt, wählen Sie *.tr-Domain bearbeiten. Wenn Sie zum Tab Erweiterungen gelangen, sehen Sie eine Warnung zu zusätzlichen Domain-Feldern. Fügen Sie es hinzu.
![img_5.png](img_5.png)
Bearbeiten Sie alle Informationen außer dem Variablennamen nach Ihren Wünschen und entfernen Sie auch das "Erforderlich"-Kontrollkästchen im Tab "Erweitert" (Wenn Sie dies nicht tun, müssen Kunden diese Felder während der Domain-Registrierung ausfüllen.)
![img_6.png](img_6.png)
