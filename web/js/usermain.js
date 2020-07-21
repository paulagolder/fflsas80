


  
   
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

   

     function clickevent(eid)
     {
        window.location.href = '/admin/event/'+eid;
     }
     function clickimage(iid,label)
     {
        window.location.href = '/admin/image/'+iid;
     }
     function clickperson(pid)
     {
        window.location.href = '/admin/person/'+pid;
     }
     function clicklocation(lid)
     {
        window.location.href = '/admin/location/'+lid;
     }
     function clickcontent(cid)
     {
        window.location.href = '/admin/content/'+cid;
     }

     
     function deletebookmark(bktype, cid)
     {
        window.location.href = '/admin/bookmark/delete/'+bktype+"/"+cid;
     }

