<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Under Maintenance — The Gentleman's Place</title>
  <style>
    :root {
      --black: #0a0805;
      --charcoal: #111008;
      --gold: #c9a84c;
      --gold-lt: #e0c06a;
      --cream: #f0e8d0;
      --cream-dim: #c4b896;
      --ff-serif: 'Playfair Display', Georgia, serif;
      --ff-sans: 'Montserrat', sans-serif;
    }
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
      background: var(--black);
      color: var(--cream-dim);
      font-family: var(--ff-sans);
      text-align: center;
      padding: 1.5rem;
    }
    .maintenance-card {
      max-width: 500px;
      width: 100%;
      border: 1px solid rgba(201, 168, 76, 0.15);
      padding: 3.5rem 2.5rem;
      background: var(--charcoal);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.7);
    }
    .maint-monogram {
      width: 48px;
      height: 48px;
      border: 1px solid var(--gold);
      margin: 0 auto 2rem;
      display: grid;
      place-items: center;
      font-family: var(--ff-serif);
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--gold);
    }
    .maint-title {
      font-family: var(--ff-serif);
      font-size: 1.75rem;
      font-weight: 600;
      color: var(--cream);
      margin: 0 0 1rem 0;
    }
    .maint-msg {
      font-size: 0.85rem;
      line-height: 1.7;
      color: var(--cream-dim);
      opacity: 0.85;
      margin: 0 0 2rem 0;
    }
    .maint-foot {
      font-size: 0.68rem;
      letter-spacing: 0.15em;
      text-transform: uppercase;
      color: var(--gold);
      opacity: 0.7;
      margin: 0;
    }
  </style>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="maintenance-card">
    <div class="maint-monogram">GP</div>
    <h1 class="maint-title">Scheduled Maintenance</h1>
    <p class="maint-msg">
      We are currently performing scheduled maintenance to enhance your experience. 
      Please check back shortly. Thank you for your patience.
    </p>
    <p class="maint-foot">The Gentleman's Place</p>
  </div>
</body>
</html>
