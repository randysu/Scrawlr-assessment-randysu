<script>
import axios from 'axios'


export default {
  data() {
    return {
      username: "",
      password:"",
      confirm_password:"",
      show_alert:false,
      alert_message:"",
      login_status:localStorage.getItem('login_status')??0,
      
    }
    

  },
  
  mounted() {
    
  },

  methods:{
    signup(event){
      if(this.password != this.password_confirm_password ){
        var signup_data = {
        "username":this.username,
        "password":this.password,
        // "confirmpassword":this.confirm_password
        }
      // console.log(signup_data);

        axios.post('http://localhost:8288/user/signup',signup_data,{
          

        }).then(response => {
          console.log(response);
          localStorage.setItem('api_token', response.data.api_token);
          localStorage.setItem('user_name', response.data.user_name);
          localStorage.setItem('login_status', 1);
        })
      }
      else{
        this.alert_message = "two passwords need to be same"
        this.show_alert = true
      }
      




      // console.log(signup_data);

    }
  }

}
</script>
<template>
  <div>
    <p>Sign up Page required</p>
    <!-- <div class="alignLeft">
      <ul>
        <li>required fields: username, password, password confirmation</li>
        <li>submit button</li>
      </ul>
    </div> -->
   
    <div v-if="login_status == 0">
      <div class="form-group" >
        <label for="exampleInputEmail1">User name</label>
        <input type="email" class="form-control"   placeholder="User name" v-model="username">
        <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
      </div>
      <div class="form-group">
        <label for="exampleInputPassword1">Password</label>
        <input type="password" class="form-control"  placeholder="Password" v-model="password">
      </div>
      <div class="form-group">
        <label for="exampleInputPassword1">Password</label>
        <input type="password" class="form-control"  placeholder="Confirm Password" v-model="confirm_password">
      </div>
      <button class="btn btn-primary mt-3" @click="signup">Signup</button>
    </div>
    <div class="alert alert-success" role="alert" v-if="login_status == 1">
      You already logged in
    </div>
    
    <div class="alert alert-danger mt-3" role="alert" v-if="show_alert">
      {{alert_message}}
    </div>
      
    
  </div>
</template>



<style scoped>
</style>


