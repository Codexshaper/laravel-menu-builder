<template>
	<div class="left-sidebar">
	    <div class="sidebar">
	      <div class="sidebar-wrapper">
	      	<div class="user-pannel"> </div>
	        <ul class="nav">
	          <li class="nav-item ">
	            <a class="nav-link" data-toggle="collapse" href="#formMenubuilder" aria-expanded="false">
	              <i class="material-icons">settings_appl</i>
	              <p> Tools
	                <b class="caret"></b>
	              </p>
	              </a>
	              <div class="collapse" id="formMenubuilder" style="">
	                <ul class="nav">
	                  <li class="nav-item active">
	                    <a class="nav-link" href="/admin/menus">
	                        <i class="material-icons">menus</i> Menu Builder
	                    </a>
	                  </li>
	                </ul>
	            </div>
	          </li>
	        </ul>
	      </div>
	    </div>
	</div>
</template>
<script>
  export default {
    name: 'left-sidebar',
    components: {
    }
  }
</script>
<style scoped>
	.left-sidebar {
		background-color: #000;
		color: #fff;
	}
</style>