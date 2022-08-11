<script>
import axios from 'axios'
import axiosRetry from 'axios-retry';

export default {
  data() {
    
    return {
      todo_notes:[],
      note_status:{
        1: 'pending',
        2 : 'pending',
        3 : 'completed',
      },
      max_page:1,
      current_page:1,
      next_page_exists:true,
      prev_page_exists:true,
      new_note:"",
      login_status:localStorage.getItem('login_status')??0,
    }
    

  },
  
  mounted() {
    this.getnotes(1)
  },

  methods:{
    pending(note_id){
      let data = {
        "api_token":localStorage.getItem('api_token'),
        // 'note':note_id
      }
      axios.post('http://localhost:8288/todonotes/mark/pending/'+note_id,data,{
          

      }).then(response => {
        console.log(response)
          this.getnotes(this.current_page)
      })
      

    },
    complete(note_id){
      let data = {
        "api_token":localStorage.getItem('api_token'),
        // 'note':note_id
      }
      
      axios.post('http://localhost:8288/todonotes/mark/done/'+note_id,data,{
          

      }).then(response => {
        console.log(response)
          this.getnotes(this.current_page)
      })
    },

    getnotes(page_number){
      if(this.login_status == 1){
        let data = {
          "api_token":localStorage.getItem('api_token'),
          'page':page_number
        }

        axios.get('http://localhost:8288/todonotes',{params:data},{
            
        }).then(response => {
            console.log(response.data);
            this.todo_notes = response.data.data;
            this.max_page = response.data.last_page;
            this.current_page = page_number;
            localStorage.setItem('local_notes:'+page_number, JSON.stringify(response.data.data));
            localStorage.setItem('max_page',response.data.last_page)
            if(this.current_page>=this.max_page){
              this.next_page_exists = false;
            }
            else{
              this.next_page_exists = true;
            }

            if(this.current_page == 1){
              this.prev_page_exists = false;
            }
            else{
              this.prev_page_exists = true;
            }
        }).catch(error=> {
          console.log('request failed')

          this.todo_notes = JSON.parse(localStorage.getItem('local_notes:'+page_number));
          this.max_page = Number(localStorage.getItem('max_page'));
          
          this.current_page = page_number;
          if(this.current_page>=this.max_page){
            this.next_page_exists = false;
          }
          else{
            this.next_page_exists = true;
          }

          if(this.current_page == 1){
            this.prev_page_exists = false;
          }
            else{
            this.prev_page_exists = true;
          }
          
        });

      }
      
    },

    creatNote(){
      let data = {
        "api_token":localStorage.getItem('api_token'),
        "note":this.new_note
      }
      axios.post('http://localhost:8288/todonotes/create',data,{
          

      }).then(response => {
          this.getnotes(1)
      })
    }
  }

}
</script>

<template>
  <div>
    <p>Todonotes Page required.</p>
    <p v-if="login_status != 1">You need to log in first</p>
    <div class="alignLeft" v-if="login_status == 1">

      <div class="form-group">
        <label for="">Create Note</label>
        <input type="text" class="form-control"  v-model="new_note">
      </div>
      <button class="btn btn-primary mt-3" @click="creatNote">Create</button>
    </div>
    <div class="alignLeft" v-if="login_status == 1">
      <div class="mt-4">Your notes</div>
      <!-- <ul>
        <li>Show list of paginated todonotes</li>
        <li>Show Next and Previous buttons (disable buttons if no page in that direction)</li>
        <li>Create todonote form</li>
        <li>Offline mode: disable connection to the api and ensure that todonotes are visible</li>
      </ul> -->
      
      <div v-for="(todo_note) in todo_notes" :key="todo_note.id">
        <!-- <p>{{i+1}} . {{todo_note.note}}</p> 
        <div>
          <button @click="complete(todo_note.id)">set complete</button>
          <button @click="pending(todo_note.id)">set pending</button>
        </div> -->
        <div class="card mt-3">
          <div class="card-header">
            {{todo_note.id}} - {{note_status[todo_note.status]}}
          </div>
          <div class="card-body">
            <p class="card-text">{{todo_note.note}}</p>
          </div>
          <div class="card-footer">
            <button type="button" class="btn btn-primary mx-2" @click="pending(todo_note.id)">Pending</button>
            <button type="button" class="btn btn-success mx-2" @click="complete(todo_note.id)">Complete</button>
          </div>
        </div>
      </div>
    
      <div class="mt-3">
        <nav aria-label="Page navigation example mt-3">
          <ul class="pagination">
            <li class="page-item" 
              @click="prev_page_exists && getnotes(current_page-1)"
            >
              <div class="page-link">Previous</div>
            </li>
            <li class="page-item" 
              v-for="n in max_page"
              v-bind:key="n"
              @click="getnotes(n)"
              :class="{active : current_page == n}"
            >
              <div class="page-link" href="#">{{n}}</div>
            </li>
            <!-- <li class="page-item"><div class="page-link" href="#">2</div></li>
            <li class="page-item"><div class="page-link" href="#">3</div></li> -->
            <li class="page-item" @click="next_page_exists && getnotes(current_page+1)">
              <div class="page-link" href="#">Next</div>
            </li>
          </ul>
        </nav>
      </div>
      

    </div>
    
  </div>
</template>

<style scoped>
/* TODO */
</style>
