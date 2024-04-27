const express = require('express');
const multer = require('multer');

const app = express();
const upload = multer({ dest: 'uploads/' });

app.post('/upload', upload.single('uploadfile'), (req, res) => {
  // Handle the uploaded file here
  console.log(req.file); // This will log information about the uploaded file

  // Return a response indicating success or failure
  res.json({ success: true });
});

app.listen(3000, () => {
  console.log('Server is running on port 3000');
});