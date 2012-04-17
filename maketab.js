M.block_fncoursemanagers_maketabs = {
    create_tab:function(Y, modid, filename, modnamearr, basename, modname, modnameexistinmodnamearr, selectedtab, add, haseditingcapability) {       
        var mainDiv = document.getElementById("region-main");    
        
        if ( ((filename === 'modedit') && (basename === 'course') && (add === '') && (haseditingcapability == '1')) || 
            ((filename === 'view') && (modnameexistinmodnamearr===true) && (haseditingcapability == '1')) ||
            ((filename === 'edit') && (basename === 'fn_coursemanager') && (haseditingcapability == '1'))
            ){
            var tabDiv = document.createElement("div");
            tabDiv.className = "tabtree";
            var ulist = document.createElement("ul");
            ulist.className = "tabrow0"
            mainDiv.childNodes[1].insertBefore(tabDiv,mainDiv.childNodes[1].childNodes[1]);
            tabDiv.appendChild(ulist);
                
            for(var i=1; i<4; i++) {
                var liList = document.createElement('li');
                var liLink = document.createElement('a');
                var liSpan = document.createElement('span');
                var liImage = document.createElement('img');

                if (i === 1) {
                    var viewPageUrl = M.cfg.wwwroot+'/mod/'+modname+'/view.php?id='+modid+'&setdefaulttab=preview'; 
                    ulist.appendChild(liList);
                    liList.appendChild(liLink);                        
                    if (selectedtab === 'Preview') {                        
                        liList.className = "first "+"onerow here selected";
                        liLink.className = "nolink";
                        var tabRowDiv1 = document.createElement('div');
                        tabRowDiv1.className = "tabrow1 "+"empty"; 
                        liList.appendChild(tabRowDiv1);
                        tabRowDiv1.appendChild(document.createTextNode("&nbsp;"));
                    } else {
                        liList.className = "first";
                        liLink.title = "Preview";
                        liLink.href = viewPageUrl;//"view.php"+modid;
                    }
                    liLink.appendChild(liSpan);
                    liSpan.appendChild(document.createTextNode("Preview"));
                        
                }
                if (i === 2) {              
                    var editContentPageUrl = M.cfg.wwwroot+'/course/modedit.php?update='+modid+'&return=0&onlygeneral=show'+'&setdefaulttab=editcontent';                   
                    ulist.appendChild(liList);
                    liList.appendChild(liLink);
                    if (selectedtab === 'Edit Content') {
                        liList.className = "onerow here selected";
                        liLink.className = "nolink";
                        var tabRowDiv2 = document.createElement('div');
                        tabRowDiv2.className = "tabrow1 "+"empty"; 
                        liList.appendChild(tabRowDiv2);
                        tabRowDiv2.appendChild(document.createTextNode("&nbsp;"));
                    } else{
                        liLink.title = "Edit Content";                
                        liLink.href = editContentPageUrl;
                    }
                    liLink.appendChild(liSpan);
                    liSpan.appendChild(document.createTextNode("Edit Content"));
                        
                       
                }
                if (i === 3) {
                    var settingPageUrl = M.cfg.wwwroot+'/course/modedit.php?update='+modid+'&return=0'+'&setdefaulttab=settings'; 
                    ulist.appendChild(liList);
                    liList.appendChild(liLink);                    
                    if (selectedtab === 'Settings') {                        
                        liList.className = "last "+"onerow here selected";
                        liLink.className = "nolink";
                        var tabRowDiv3 = document.createElement('div');
                        tabRowDiv3.className = "tabrow1 "+"empty"; 
                        liList.appendChild(tabRowDiv3);
                        tabRowDiv3.appendChild(document.createTextNode("&nbsp;"));                       
                    } else{                        
                        liList.className = "last"
                        liLink.title = "Settings";
                        liLink.href = settingPageUrl;
                    }
                        
                    liLink.appendChild(liSpan);
                    liSpan.appendChild(document.createTextNode("Settings"));
                        
                }
            }
        }
    }
    
}