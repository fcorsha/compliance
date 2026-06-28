# BRAND.md — S-TEAM

## לוגו
- קובץ: `assets/logo.svg`
- פורמט: SVG (וקטורי, רקע שקוף)
- שימוש: לוגו ראשי בכל הדפים

---

## לוח צבעים

| תפקיד           | שם           | HEX       | שימוש                            |
|-----------------|--------------|-----------|----------------------------------|
| רקע כהה         | Navy Dark    | `#1a2232` | רקע Shield, Hero section         |
| רקע כללי        | Navy         | `#1e2a3a` | רקע אתר (אם בוחרים כהה)         |
| זהב בהיר        | Gold Light   | `#EDD068` | Highlight, כותרות ראשיות        |
| זהב ראשי        | Gold Main    | `#C9973E` | כפתורים, אייקונים, accent        |
| זהב כהה         | Gold Dark    | `#8B6420` | צל, border תחתון                 |
| לבן             | White        | `#FFFFFF` | טקסט על רקע כהה                 |
| אפור בהיר       | Gray Light   | `#F5F5F5` | רקע sections בהירים              |
| אפור טקסט       | Gray Text    | `#555555` | טקסט גוף                         |

---

## גרדיאנט זהב (לשימוש ב-CSS)
```css
/* גרדיאנט זהב ראשי */
background: linear-gradient(180deg, #EDD068 0%, #C9973E 50%, #8B6420 100%);

/* זהב כטקסט */
background: linear-gradient(180deg, #EDD068 0%, #C9973E 50%, #A07830 100%);
-webkit-background-clip: text;
-webkit-text-fill-color: transparent;
background-clip: text;
```

---

## פונטים

| תפקיד        | פונט                        | Google Fonts                              |
|--------------|-----------------------------|-------------------------------------------|
| ראשי (RTL)   | Assistant                   | `fonts.google.com/specimen/Assistant`     |
| לוגו / דגש   | Georgia (serif)             | מובנה בדפדפן                              |

```html
<!-- בתוך <head> -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Assistant:wght@400;600;700&display=swap" rel="stylesheet">
```

```css
body {
  font-family: 'Assistant', sans-serif;
  direction: rtl;
  text-align: right;
}
```

---

## אופי ויזואלי
- **מקצועי ואמין** — לא קליל, לא צעיר מדי
- **סטייל:** כהה (Navy) + זהב — אבטחה, יוקרה, רצינות
- **אין:** ניאון, gradients צבעוניים, אנימציות מוגזמות

---

## סטטוס אישור
- [x] לוגו SVG — **מאושר**
- [x] צבעים — **מאושרים** (נגזרו מהלוגו)
- [x] פונטים — **מאושרים**
- [ ] מוקאפ Hero — ממתין לאישור
