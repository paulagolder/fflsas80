

function xmyFunction() {
    window.location.href = "/mailto";
}


    
                
    function HideMe( divid) 
    {
      var x = document.getElementById(divid);
      if (x.style.display === "none") 
      {
        //x.style.display = "block";
        x.style.display= "flex";
      } else 
      {
        x.style.display = "none";
      }
   } 
   
     function HideMe2( divid) 
    {
      var x = document.getElementById(divid);
      if (x.style.display === "none") 
      {
        x.style.display = "block";
        //x.style.display= "flex";
      } else 
      {
        x.style.display = "none";
      }
   } 

   function myFunction(img)
   {
          document.body.style.backgroundImage = img;
      
   }

   function ConfirmDelete() 
   {
      return confirm("Are you sure you want to delete?");
   }
