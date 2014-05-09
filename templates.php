body {
  font-family: "Trebuchet MS", Helvetica, Arial, sans-serif;
  color: #333;
  width: 75%;
  max-width: 50em;
  margin: 1em auto 1em auto;
  overflow: auto;
  }
a:link {
  color: #2366B6;
  text-decoration: underline;
  }
a:visited {
  color: #2B1558;
  text-decoration: underline;
  }
a:active {
  color: #458FC7;
  text-decoration: underline;
  }
a:hover {
  color: #7A0000;
  text-decoration: none;
  }
h1, h2, h3, h4, h5, h6 {
  font-family: Georgia, "Times New Roman", serif;
  font-weight: normal;
  }
h2, h3, h4, h5, h6 {
  color: #663534;
  font-size: 1.1em;
  margin: 2em 0 0.2em 0;
  } 
h1 {
  font-size: 3em;
  letter-spacing: 0.2em;
  border-bottom: 1px solid #333;
  }
h2 {
  padding: 0 0 0.3em 3%;
  font-size: 1.5em;
  border-bottom: 1px solid #cecece;
  text-transform: uppercase;
  }
h3{
  padding: 0 0 0.5em 3%;
  font-size: 1em;
  text-transform: uppercase;
  }
h4 {
  padding: 0 0 0.5em 3%;
  font-size: 1em;
  }
h5 {
  padding: 0 0 0.5em 6%;
  font-size: 1em;
  }
h6 {
  padding: 0 0 0.5em 9%;
  font-size: 1em;
  }
ul, ol {
  margin: 1em 2em 1em 2em;
  padding-left: 1em;
  }
ul ul, ol ol, ul ol, ol ul {
  margin: 0;
  padding-left: 1em;
  }
p {
  margin: 1em 0 1em 0;
  }
legend {
  font-size: 1.5em;
  text-transform: uppercase;
  }
button {
  border: 1px outset #333;
  padding: 0.1em;
  margin: 0.5em 0 0.2em 0;
  }
input, select, textarea {
  display: block;
  border: 1px inset #333;
  padding: 0.1em;
  width: 100%;
  margin-bottom: 1em;
  }
input[type="checkbox"] {
  display: inline;
  width: auto;
  margin: 5px;
  }
pre {
  margin: 2em;
  font-family: Consola, monospace;
  overflow: auto;
  }
  
#outputPane {
  display: none;
  }
#syntaxPane {
  display: none;
  }
#previewPane {
  overflow: auto;
  }

#admin .comment {
  border: 1px dashed #333;
  margin: 0.5em;
  padding: 0.5em;
}
#admin input[readonly="readonly"] {
  background-color: #ccc;
}
