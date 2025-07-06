<?php
   defined( 'ABSPATH' ) || die( 'No direct script access allowed.' );
?>
<div class="container">
   <div class="row">
      <div class="col-sm-12">
         <form id="custom-form" enctype="multipart/form-data" class="container mt-4 p-4 border rounded bg-light" method="post">
            <h4 class="mb-4">User Registration</h4>
            <div class="mb-3">
               <label for="name" class="form-label">Full Name:</label>
               <input type="text" name="name" id="name" class="form-control" placeholder="Enter your name" />
            </div>
            <div class="mb-3">
               <label for="email" class="form-label">Email Address:</label>
               <input type="email" name="email" id="email" class="form-control" placeholder="example@email.com" />
            </div>
            <div class="mb-3">
               <label for="phone" class="form-label">Phone Number:</label>
               <input type="tel" name="phone" id="phone" class="form-control" placeholder="e.g. 0300-1234567" required>
            </div>
            <div class="mb-3">
               <label class="form-label">Gender:</label>
               <select name="gender" id="gender" class="form-select" required>
                  <option value="">Select Gender</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="other">Other</option>
               </select>
            </div>
            <div class="mb-3">
               <label for="country" class="form-label">Country:</label>
               <input type="text" name="country" id="country" class="form-control" placeholder="e.g. Pakistan" required>
            </div>
            <div class="mb-3">
               <label for="image_path" class="form-label">Profile Picture:</label>
               <input type="file" name="image_path" id="image_path" class="form-control" accept=".jpg,.png" />
            </div>
            <button type="submit" class="btn btn-primary" id="submit-btn">
            <span class="btn-text">Register</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
            <div id="form-message" class="mt-3"></div>
         </form>
      </div>
   </div>
</div>