<script>
import axios from 'axios'
export default {
  data() {
    return {
      login_status:localStorage.getItem('login_status')??0,
      api_token:localStorage.getItem('api_token'),
    }
    

  },
  methods:{
    logout(){
      if(this.login_status == 1){
        let logout_data = {
        "api_token":this.api_token
      }
      // localStorage.removeItem('api_token');
      // localStorage.removeItem('user_name');
      // localStorage.setItem('login_status', 0);
      
      // 
      
      

      axios.post('http://localhost:8288/user/logout',logout_data,{
        

      }).then(response => {
        
        if(response.data.status == 'success'){
          localStorage.removeItem('api_token');
          localStorage.removeItem('user_name');
          localStorage.setItem('login_status', 0);
          this.login_status = 0;
          

        }
        
        
      })


      }
      


    }



  }

}
</script>

<template>
  <div>
    <div id="nav">
      <router-link to="/">Home</router-link> |
      <router-link to="/signup">Signup</router-link> | 
      <router-link to="/login">Login</router-link> | 
      <router-link to="/todonotes">TODO Notes</router-link>
      <!-- Should only show if still logged in -->
      | <a href="javascript:" @click="logout">Logout</a>
    </div>
    <router-view></router-view>
  </div>
</template>

<style>
.alignLeft {
  text-align: left;
}
</style>
