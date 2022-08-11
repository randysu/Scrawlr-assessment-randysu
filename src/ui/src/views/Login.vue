<script>

import axios from 'axios'

  export default {
  data() {
    return {
      username: "",
      password:"",
      login_status:localStorage.getItem('login_status')??0,
      api_token:localStorage.getItem('api_token'),
      user_name:localStorage.getItem('user_name'),
    }
    

  },
  
  mounted() {
    console.log(this.login_status)
  },

  methods:{
    login(event){
      let login_data = {
        "username":this.username,
        "password":this.password,
        // "confirmpassword":this.confirm_password
      }
      console.log(login_data);

      axios.post('http://localhost:8288/user/login',login_data,{
        

      }).then(response => {
        console.log(response);
        if(response.data.status == "success"){
          localStorage.setItem('api_token', response.data.api_token);
          localStorage.setItem('user_name', response.data.user_name);
          localStorage.setItem('login_status', 1);
          this.login_status = 1;
          this.user_name = response.data.user_name;
        }
        
        
      })

    },
    logout(event){
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
</script>


<template>
  <div>
    <p>Login Page required</p>
    
    <div  v-if="login_status == 1">
      <div class="alert alert-success" role="alert" >
      <p>You already logged in, user name is {{user_name}}</p>
      </div>
      <button @click="logout">Log out</button>
    </div>
    <div v-if="login_status != 1">
      <div class="form-group">
        <label for="">User name</label>
        <input type="email" class="form-control"   placeholder="User name" v-model="username">
        <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
      </div>
      <div class="form-group">
        <label for="">Password</label>
        <input type="password" class="form-control"  placeholder="Password" v-model="password">
      </div>
      <button class="btn btn-primary mt-3" @click="login">Login</button>
    </div>
    
  </div>
</template>

<style scoped>
</style>
