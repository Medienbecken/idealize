document.addEventListener('DOMContentLoaded', function () {

	var modal = document.getElementById('OMLlogin');
	
	function openLoginModal () { 
		modal.style.display = 'block';
	}
	
	var hash = window.location.hash;
	
		   if (hash.substring(1) == 'login') {
				modal.style.display = 'block';
		   }
	
		   var osmlogin = document.querySelectorAll('.osmlogin');
		   var alogin = document.querySelectorAll('.alogin');
		   var clogin = document.querySelectorAll('.login-modal-close')[0]; 
	

			for (const shortlogin of alogin) {
			
				if (typeof(shortlogin) != 'undefined' && shortlogin != null) {
					shortlogin.addEventListener('click', function(e) {
						e.preventDefault();   
						if (typeof(modal) != 'undefined' && modal != null) {
							modal.style.display = 'block';
						}
					});
				}
		}
	
			if (typeof(clogin) != 'undefined' && clogin != null) {
				clogin.addEventListener('click', function(e) {
					e.preventDefault();  
					modal.style.display = 'none';
				});
			}
	 
			for (const menulogin of osmlogin) {
			
				if (typeof(menulogin) != 'undefined' && menulogin != null) {
					menulogin.addEventListener('click', function(e) {
						e.preventDefault();   
						if (typeof(modal) != 'undefined' && modal != null) {
							modal.style.display = 'block';
						}
					});
				}
		}
	
			 
	
			 document.addEventListener('click', function(e) {
	
					if(e.target == modal && e.target!=alogin) {
					modal.style.display = 'none';
				   }  else {  }
			}); 
			
	});
	