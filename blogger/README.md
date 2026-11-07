# Royal Vision Store: Blogger setup

Is folder mein do files hain:
- `royal-vision-store.xml`: Blogger theme (design, cart, checkout)
- `order-sheet.gs`: Google Sheet mein orders save karne ka script

## 1. Theme lagana (aik dafa)

1. Blogger → **Theme** → "Customize" ke saath wala **▼** → **Backup** (purani theme download kar lein).
2. Phir **▼** → **Edit HTML** → andar sab select karein (Ctrl+A) → delete.
3. `royal-vision-store.xml` ka poora content copy kar ke paste karein → **Save**.
4. Agar Blogger poochay "widgets delete honge", to **Delete widgets** dabayein.

## 2. Blogger settings

- **Settings → Posts → Max posts shown on main page: 12**
- **Settings → Comments → Comment location: Hide**
- **Settings → HTTPS redirect: On**

## 3. Product kaise add karein (koi coding nahi)

Blogger → **New Post**:

- **Title:** product ka naam, jaise `Lilac Khaddar 3-Piece Suit`
- **Post ke shuru mein** yeh lines likhein. Jo zaroori na ho woh chhor dein:

  ```
  Price: 4500
  Sale Price: 3990
  Sizes: S, M, L, XL
  Colors: Lilac, Black
  Fabric: Khaddar
  Code: RV-101
  ```
- Phir **photos** insert karein. Pehli photo card par dikhegi, baqi gallery mein.
- Phir product ki **description** likhein.
- Right side **Labels** mein category likhein, jaise `Ladies Khaddar`. Har label menu mein khud aa jayega.
- **Publish**.

Product khatam ho jaye to `Stock: out` likh dein. "Sold out" dikhega aur order band ho jayega.

Price ki lines customer ko description mein nazar nahi aatin. Theme unhein price, size buttons aur details mein badal deti hai.

## 4. Store Settings (WhatsApp, delivery, payment)

Blogger → **Layout** → **Store Settings** gadget → edit → har line mein `naam: value`:

```
whatsapp: 03227782932
sheet: https://script.google.com/macros/s/XXXX/exec
delivery: 250
free_over: 5000
delivery_days: 3–5 working days
announce: Winter Sale is live · Cash on Delivery
jazzcash: 03XX-XXXXXXX (Account title)
easypaisa: 03XX-XXXXXXX (Account title)
bank: Bank Alfalah · Account title · IBAN
email: orders@example.com
hero_title: Elegant suits, delivered to your door
hero_text: Khaddar, karandi and unstitched collections.
hero_image: https://...photo link...
tagline: Quality suits from Faisalabad, delivered all over Pakistan.
exchange: 7-day exchange on unused items
```

- `delivery: 0` likhein to har jagah "Free delivery" dikhega.
- `jazzcash`, `easypaisa` aur `bank` sirf tab checkout par aate hain jab yahan likhe hon. Cash on Delivery hamesha rehta hai.
- `exchange` sirf tab likhein jab yeh policy aap waqai dete hain.

## 5. Orders Google Sheet mein

1. Google Sheets mein nayi sheet banayein, naam: `Royal Vision Orders`.
2. **Extensions → Apps Script** → sample code delete → `order-sheet.gs` paste → Save.
3. **Deploy → New deployment** → type **Web app**:
   - Execute as: **Me**
   - Who has access: **Anyone**
   - Deploy → permissions **Allow**
4. Jo **Web app URL** mile, Store Settings mein `sheet:` ke aage paste karein.

Har order sheet ke "Orders" tab mein aayega aur aap ko email bhi aayegi. Customer ko "Send order on WhatsApp" button milta hai, jis se order ki poori detail aap ke WhatsApp par aati hai.

## 6. Help pages

Blogger → **Pages** → yeh pages banayein: Contact Us, Return Policy, Privacy Policy, Shipping & Delivery. Phir **Layout → Help gadget** mein inhein tick karein. Yeh footer mein dikhenge.
