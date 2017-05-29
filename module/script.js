"use strict";
/**
*    Declaration of your module
*    @params modulename and undefined
**/
(function(talentTree,undefined) { 

    const HOST = 'https://talenttree-alesanchezr.c9users.io/api/';
    let studentId, 
        profileId,
        parentId,
        allSpecialties = [],
        percents = [],
        allBadges = [];

    talentTree.init = function(theSettings){
        //this function initialize your module
        if(!theSettings) theSettings = {};
        if(theSettings.studentId) studentId = theSettings.studentId;
        if(theSettings.profileId) profileId = theSettings.profileId;
        if(theSettings.parentId) parentId = theSettings.parentId;
        retriveProfileInformation();
    }
    
    function retriveProfileInformation(){
    
        ajax.get(HOST+'specialties/profile/'+profileId, {}, function(reponse) {
            if(reponse.code==200)
            {
                allSpecialties = reponse.data;
                for(var i = 0; i<reponse.data.length;i++) 
                {
                    var badges = reponse.data[i].badges;
                    for(var j = 0; j<badges.length;j++) 
                    {
                        badges[i].points_acumulated = 0;
                        badges[i].is_achived = 0;
                        allBadges[badges[j].slug] = badges[i];
                    }
                }
                retriveStudenInformation();
            }
            
        });
    }
    
    function renderSpecialties(){
    
        let content = '<ul class="specialty">';
        for(var i = 0; i<allSpecialties.length;i++)
        {
            content += '<li>';
                content += '<h2>'+allSpecialties[i].name+'</h2>';
                content += renderSpecialtiyBages(allSpecialties[i].badges);
            content += '</li>';
        }
        content += '</ul>';
        return content;
    }
    
    
    function renderSpecialtiyBages(allBadges){
    
        let content = '<ul class="badge">';
        for(var i = 0; i<allBadges.length;i++)
        {
            content += '<li class="'+allBadges[i].slug+'">';
            content += printBadge(percents[allBadges[i].slug]);
            content += '<span>'+allBadges[i].name+'</span>';
            content += '</li>';
        }
        content += '</ul>';
        return content;
    }
    
    function printBadge(percent){
        
        var csspercent = percent;
        if(isNaN(percent)) percent = 0;
        if(csspercent>99) csspercent = 100;
        let content = '<div class="avatar-container p-'+csspercent+'">';
        content += '<div alt="" class="avatar"></div>';
        content += '<div class="info js-active"><div class="info-inner">'+percent+'%</div></div>';
        content += '</div>';
        return content;
    }
    
    function retriveStudenInformation(){
    
        ajax.get(HOST+'badges/student/'+studentId, {}, function(response) {
            if(response.code==200)
            {
                var badges = response.data;
                for(var i = 0; i<badges.length;i++) 
                {
                    if(allBadges[badges[i].slug])
                    {
                        allBadges[badges[i].slug].points_acumulated = badges[i].points_acumulated;
                        allBadges[badges[i].slug].is_achived = badges[i].is_achived;
                        let percent = (badges[i].points_acumulated/badges[i].points_to_achieve)*100;
                        allBadges[badges[i].slug].percent = percent;
                        percents[badges[i].slug] = percent;
                    }
                }
                
                document.querySelector(parentId).innerHTML = renderSpecialties();
            }
        });
    }

})( window.talentTree = window.talentTree || {} );