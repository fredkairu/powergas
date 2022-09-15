<html>  
<head>  
<title>dropdown menu using button</title>  
</head>  
<style>  
/* set the position of dropdown */  
.dropdown {  
     position: relative;  
     display: inline-block;  
}  
/* set the size and position of button on web */  
.button {  
     padding: 10px 30px;  
     font-size: 15px;  
}  
/* provide css to background of list items */   
#list-items {  
    display: none;  
    position: absolute;  
    background-color: white;  
    min-width: 185px;  
}  
/* provide css to list items */   
#list-items a {  
     display: block;  
     font-size: 18px;  
     background-color: #ddd;  
     color: black;  
     text-decoration: none;  
     padding: 10px;  
}  
</style>  
  
<script>  
      //show and hide dropdown list item on button click  
      function show_hide() {  
         var click = document.getElementById("list-items");  
         if(click.style.display ==="none") {  
            click.style.display ="block";  
         } else {  
            click.style.display ="none";  
         }   
      }  
   </script>  
  
<body>  
<div class="dropdown">  
  <button onclick="show_hide()" class="button">Choose Language</button>  
   <center>  
      <!-- dropdown list items will show when we click the drop button -->   
      <div id="list-items">  
         <a href="#"> Hindi </a>  
         <a href="#"> English </a>  
         <a href="#"> Spanish </a>  
         <a href="#"> Chinese </a>  
         <a href="#"> Japanese </a>  
      </div>  
   </center>  
     
</body>  
</html>