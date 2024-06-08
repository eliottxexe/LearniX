const express = require('express');
const {OAuth2Client} = require('google-auth-library');
const path = require('path');
const app = express();

const CLIENT_ID = '51980468173-rj4nqni8j111r17gm2pe0b631oas1u84.apps.googleusercontent.com';
const CLIENT_SECRET = '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';
const REDIRECT_URI = 'http://localhost:8000/oauth2callback';

const oauth2Client = new OAuth2Client(CLIENT_ID, CLIENT_SECRET, REDIRECT_URI);

app.use(express.static(path.join(__dirname, 'public'))); // Assume static files are in 'public' directory

app.get('/auth/google', (req, res) => {
  const url = oauth2Client.generateAuthUrl({
    access_type: 'offline',
    scope: ['profile', 'email'],
  });
  res.redirect(url);
});

app.get('/oauth2callback', async (req, res) => {
  const {tokens} = await oauth2Client.getToken(req.query.code);
  oauth2Client.setCredentials(tokens);
  const ticket = await oauth2Client.verifyIdToken({
    idToken: tokens.id_token,
    audience: CLIENT_ID,
  });
  const payload = ticket.getPayload();
  res.send(`Hello ${payload.name}`);
});

app.listen(8000, () => {
  console.log('Server started on http://localhost:8000');
});
