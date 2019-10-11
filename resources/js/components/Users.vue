<template>
    <div class="container">
        <div class="row mt-5">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">User Table</h3>

                <div class="card-tools">
                  <button class="btn btn-success" @click="newmodal">Add New <i class="fa fa-user-plus fa-fw"></i></button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <tbody>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Type</th>
                      <th>Created at</th>
      
                      <th>Modify</th>
                    </tr>
                  <tr v-for="user in users" :key="user.id">
                    <td>{{ user.id }}</td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td><span class="tag tag-success">{{ user.type | upText}}</span></td>
                    
                    <td>{{ user.created_at | mydate}}</td>
                    <td>
                      <a href="#" @click="editmode(user)">
                        <i class="fa fa-edit"></i>
                      </a>
                      /
                      <a href="#" @click="deleteuser(user.id)">
                        <i class="fa fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                 
                </tbody></table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>

    <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" v-show="!editmodal"  id="addNewLabel">Add New</h5>
            <h5 class="modal-title" v-show="editmodal" id="addNewLabel">Update User</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <form @submit.prevent="editmodal ? updateuser() : createuser()">
          <div class="modal-body">

            <div class="form-group">
            
              <input v-model="form.name" type="text" name="name" placeholder="Name"
                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
              <has-error :form="form" field="name"></has-error>
            </div>

              <div class="form-group">
            
                <input v-model="form.email" type="email" name="email" placeholder="Email"
                  class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                <has-error :form="form" field="email"></has-error>
              </div>
              <div class="form-group">
            
                <textarea v-model="form.bio" type="text" name="bio" placeholder="Bio"
                  class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }">
                </textarea><has-error :form="form" field="bio"></has-error>
              </div>

              <div class="form-group">
            <select name="type" v-model="form.type" id="type" class="form-control" :class="{
               'is-invalid': form.errors.has('type') }">
               <option value="">Select User Role</option>
               <option value="admin">Admin</option>
               <option value="user">Standard User</option>
               <option value="author">Author</option>
            </select>
                <has-error :form="form" field="type"></has-error>
              </div>

                <div class="form-group">
            
                <input v-model="form.password" type="password" name="password" placeholder="Password"
                  class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                <has-error :form="form" field="password"></has-error>
              </div>


          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            <button v-show="editmodal" type="submit" class="btn btn-success">Update</button>
            <button v-show="!editmodal" type="submit" class="btn btn-primary">Create</button>
          
          </div>
          </form>
        </div>
      </div>
    </div>
    </div>

    
</template>

<script>
    export default {
        data(){//data for db
            
            return {
              
              editmodal: false,
              users : {},//users starts as empty object
              form: new Form({ 
                  id: '',
                  name: '',
                  email: '',
                  password: '',
                  type: '',
                  bio: '',
                  photo: ''
              })
            }
        },
        methods:{
          updateuser(){
            this.$Progress.start();
              this.form.put('api/user/'+this.form.id)
              .then(() => {
                $('#addNew').modal('hide');
                 Swal.fire(
                                'Updated!',
                                'Your file has been Updated.',
                                'success'
                              )
                    this.$Progress.finish();
                    Fire.$emit('Aftercreate');
              })
              .catch(() => {
                  this.$Progress.fail();
              });
          },
          editmode(user){
            this.editmodal = true;
            this.form.reset()
            $('#addNew').modal('show');
            this.form.fill(user);
          },
          newmodal(){
            this.editmodal = false;
            this.form.reset()
            $('#addNew').modal('show');
          },
            deleteuser(id){
              Swal.fire({
                      title: 'Are you sure?',
                      text: "You won't be able to revert this!",
                      type: 'warning',
                      showCancelButton: true,
                      confirmButtonColor: '#3085d6',
                      cancelButtonColor: '#d33',
                      confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {

                      //send an ajax request to server
                      if (result.value) {
                            this.form.delete('api/user/'+id).then(()=>{
                            
                              Swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                              )
                              Fire.$emit('Aftercreate');
                          }).catch(()=>{
                            Swal("Failed!", "There was something wrong", "warning");
                          });
                      }
                      
                    })
          },
            loadusers(){//get request to fetch all data
                axios.get("api/user").then(({ data }) => (this.users = data.data));
            
            },
            createuser(){
              this.$Progress.start();//progress bar start
              this.form.post('api/user')
              .then(()=>{
                  Fire.$emit('Aftercreate');
                  $('#addNew').modal('hide');//modal id for notification
                  Toast.fire({
                  type: 'success',
                  title: 'User created successfully'
                  })    
                   
              this.$Progress.finish();
              })
              .catch(()=>{

              });//post request to api user
              
            
              
            }
        },
        created() {
            this.loadusers();//loading users
            Fire.$on('Aftercreate',() => {this.loadusers();} );
            //setInterval(() => {//send http request after every 3 seconds
            //  this.loadusers();
            //}, 3000);

            }
    }
</script>
