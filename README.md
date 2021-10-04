<h1>
    <p align="center">Edison Avenue Consulting LLC QR Code Generator for WordPress</p>
</h1>

---

This is the public repository for the Edison Avenue Consulting LLC plugin that can be used to
generate QR codes for Wordpress. 

The plugin can be called via a URL argument or Via shortcode. There is a special method
for generating VCard formatted data

**Examples:**

*Shortcode For a random text:* <br />
[eac_qrcode_generate data="This is so cool"]

*Shortcode for a URL* <br />
`[eac_qrcode_generate data="https://www.edisonave.com"]
`

This will give you a QR code like the following: <br />
![Edison Avenue Consulting URL QR Code](images/edisonave_qr.png)


*Set colors and data* <br />
`[eac_qrcode_generate width="200"  height="200" bgcolor="ff0000" spacecolor="00ff00" mcolor="0000ff" data="https://www.edisonave.com"]
`

This will give you a colorized QR Code like the following: <br />
![Edison Avenue Consulting URL Colorized QR Code](images/eac_url_color.png)

*Generate  VCard QR Code* <br />
`[eac_varcard_qrcode_generate first_name="Tom" last_name="Printy" title="CEO" org="Edison Avenue Consulting LLC" email="info@edisonave.com" tel="+18472356267" url="https://www.edisonave.com"]
`

This will give you a QR Code that has VCard (contact information) QR code: <br />
![Tom Printy VCard QR Code](images/tomvcard.png)


**Intallation:**

1. Download the plugin or install the plugin from the WordPress.org plugins directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the shortcodes to the posts where you would like the QR Code to appear.

That is all there is to it.

---

