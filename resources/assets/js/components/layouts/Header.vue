<template>
	<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
	    <div class="container">
	        <a class="navbar-brand" href="/">
	            {{ config.app.name }}
	        </a>
	        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
	            <span class="navbar-toggler-icon"></span>
	        </button>

	        <div class="collapse navbar-collapse" id="navbarSupportedContent">
	            <!-- Left Side Of Navbar -->
	            <ul class="navbar-nav mr-auto">
	            </ul>

	            <!-- Right Side Of Navbar -->
	            
	        </div>
	    </div>
	</nav>
</template>
<script>
	export default {
		name: 'menu-header',
		components: {

		},
		data(){
			return {
				config: {
					app: {
						name: 'Laravel'
					}
				}
			};
		}
	}
</script>