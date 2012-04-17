M.block_fncoursemanagers_Show_Edit_Content = {
    init: function(Y, show) {        
        // there are 5 field set in and one div in mform1 which show all the setting 
        // we only show first and last field set and hide all the other        
        this.Y = Y; 
        var mform1 = Y.one('#mform1');
        if(show === "show"){
            if(mform1){
                var children_of_mform1 = mform1.get('children');
                // or call each() to do more work on each Node
                children_of_mform1.each(function (fieldsetordiv) {                
                    if((fieldsetordiv.get('id')!=='general')){
                         if(fieldsetordiv.hasClass('hidden')==false){
                            fieldsetordiv.setStyle('display', 'none'); 
                         }                                      
                    }                
                });
            }      
        }
    }
}/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


