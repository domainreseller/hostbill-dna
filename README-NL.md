<div align="center">  
  <a href="README.md"   >   TR <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/TR.png" alt="TR" height="20" /></a>  
  <a href="README-EN.md"> | EN <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/US.png" alt="EN" height="20" /></a>  
  <a href="README-AZ.md"> | AZ <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/AZ.png" alt="AZ" height="20" /></a>  
  <a href="README-DE.md"> | DE <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/DE.png" alt="DE" height="20" /></a>  
  <a href="README-FR.md"> | FR <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/FR.png" alt="FR" height="20" /></a>  
  <a href="README-AR.md"> | AR <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/AR.png" alt="AR" height="20" /></a>  
  <a href="README-NL.md"> | NL <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/NL.png" alt="NL" height="20" /></a>  
</div>

# Overzicht

**DomainNameApi** is een toonaangevende domeinnaam registrar die domeinnaam registratie en andere online diensten levert aan kleine en thuisgebaseerde bedrijven, individuen, verkeersaggregatoren en wederverkopers. HostBill stelt u in staat om de **DomainNameApi** domeinprovisioning en -beheer te automatiseren.

## Het activeren van de module
Eerst moet u bestanden uploaden naar uw HostBill directory:

Om de module te activeren, logt u in op uw HostBill admin panel, gaat u naar Instellingen → Modules → Domein Modules, vindt en kiest u de **DomainNameApi** module en klikt u op Activeren.

![](image.jpg)

## Module configuratie

Zodra u de module activeert, wordt u doorgestuurd naar de module configuratiepagina. Om de geactiveerde module te configureren, kunt u ook naar Instellingen → Apps → Nieuwe App toevoegen gaan.

Vul de configuratievelden in:

- Naam van de toepassing
- Gebruikersnaam
- Wachtwoord

Ga vervolgens verder met het toevoegen van uw nameservers:

- Primaire Nameserver
- Primaire Nameserver IP

Gebruik de Test Configuratie om te controleren of HostBill kan verbinden.

Klik op Nieuwe App toevoegen.

# Domein Import

Ga naar Extra's -> Importeren -> Diensten importeren. De Domainnameapi module zou in de lijst moeten verschijnen. Selecteer deze en klik op Doorgaan.
![img_1.png](img_1.png)
Domeinnamen worden weergegeven. U kunt klanten en producten koppelen en importeren.

![img.png](img.png)

# Domein Prijzen

Ga naar Extra's -> Importeren -> TLD Prijzen, Maak een profiel aan.
Vul zorgvuldig de tarieven en andere instellingen in.

![img_2.png](img_2.png)

# Domein Query Instellingen

Om query-instellingen voor een extensie in te stellen, ga naar het tabblad Instellingen -> Domein Instellingen.
![img_3.png](img_3.png)
Bij het bewerken van de extensie, selecteer Domainnameapi als Whois engine en Conformiteitscontrole. Op deze manier worden uw queries via Domainnameapi uitgevoerd in plaats van algemene whois servers.
Opmerking: Voor deze bewerking moet u het bestand whois.custom.php.example in de Hostbill Directory > includes > extend > whois hernoemen naar whois.custom.php.

![img_4.png](img_4.png)

# Vereisten voor TR Domein Registratie Proces
Ga naar Instellingen -> Producten -> Selecteer uw domein product, selecteer om *.tr domein te bewerken. Wanneer u bij het tabblad Extensies komt, ziet u een waarschuwing over extra domeinvelden. Voeg deze toe.
![img_5.png](img_5.png)
Bewerk alle informatie behalve de variabele naam naar wens, en verwijder ook het "Verplicht" vinkje in het tabblad "Geavanceerd" (Als u dit niet doet, moeten klanten deze velden invullen tijdens domeinregistratie.)
![img_6.png](img_6.png)
