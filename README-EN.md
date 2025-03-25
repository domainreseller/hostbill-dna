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


# Overview

**DomainNameApi** is a leading domain name registrar providing domain name registration and other online services to small and home-based businesses, individuals, traffic aggregators and resellers. HostBill allows you to automate **DomainNameApi** domain provisioning and management.

## Activating the module
First you need to upload files to your HostBill directory:

In order to activate the module login to your HostBill admin panel, go to Settings → Modules → Domain Modules, find and choose **DomainNameApi** module and click Activate.

![](image.jpg)

## Module configuration

Once you activate the module you will be redirected to module configuration page. To configure the activated module you can also go to Settings → Apps → Add New App.

Fill in the configuration fields:

- Name of the application
- Username
- Password

Then proceed to adding your nameservers:

- Primary Nameserver
- Primary Nameserver IP

Use Test Configuration to check if HostBill can connect.

Click on Add New App.

# Domain Import

Go to Extras -> Import -> Import Services. The Domainnameapi module should appear in the list. Select it and click Continue.
![img_1.png](img_1.png)
Domain names will be listed. You can match customers and products and import them.

![img.png](img.png)

# Domain Pricing

Go to Extras -> Import -> TLD Prices, Create a profile.
Carefully fill in the rates and other settings.

![img_2.png](img_2.png)

# Domain Query Settings

To set query settings for an extension, go to Settings -> Domain Settings tab.
![img_3.png](img_3.png)
When editing the extension, select Domainnameapi as the Whois engine and Compliance check. This way, your queries will be made through Domainnameapi instead of general whois servers.
Note: For this operation, you need to rename the whois.custom.php.example file in the Hostbill Directory > includes > extend > whois to whois.custom.php.

![img_4.png](img_4.png)

# Prerequisites for TR Domain Registration Process
Go to Settings -> Products -> Select your domain product, select to edit *.tr domain. When you come to the Extensions tab, you will see a warning about additional domain fields. Add it.
![img_5.png](img_5.png)
Edit all information except the variable name as you wish, and also remove the "Required" mark in the "Advanced" tab (If you don't do this, customers will have to fill in these fields during domain registration.)
![img_6.png](img_6.png)

