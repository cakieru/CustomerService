<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>E-Commerce Module</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Inter',sans-serif;
}

body{

background:#0d1117;
color:white;
min-height:100vh;
padding:40px;

animation:fadeIn .8s;
}

@keyframes fadeIn{

from{
opacity:0;
}

to{
opacity:1;
}

}

.container{

max-width:1200px;
margin:auto;
display:grid;
grid-template-columns:340px 1fr;
gap:30px;

}

.card{

background:#161b22;
border:1px solid rgba(255,255,255,.08);
border-radius:18px;
padding:25px;
box-shadow:0 20px 40px rgba(0,0,0,.35);
animation:slideUp .6s ease;

}

@keyframes slideUp{

from{
opacity:0;
transform:translateY(30px);
}

to{
opacity:1;
transform:translateY(0);
}

}

h2{

font-size:28px;
margin-bottom:20px;
font-weight:700;

}

.section-title{

font-size:13px;
text-transform:uppercase;
letter-spacing:1px;
margin-bottom:18px;
color:#8b949e;

}

label{

display:block;
margin-top:18px;
font-size:13px;
margin-bottom:6px;
color:#b8c1cc;

}

input{

width:100%;
padding:12px;
background:#0d1117;
border:1px solid #30363d;
border-radius:10px;
color:white;
transition:.3s;

}

input:focus{

outline:none;
border-color:#3b82f6;

}

button{

margin-top:20px;
padding:13px;
width:100%;
background:#2563eb;
border:none;
color:white;
border-radius:10px;
font-weight:600;
cursor:pointer;
transition:.3s;

}

button:hover{

background:#1d4ed8;
transform:translateY(-2px);

}

table{

width:100%;
border-collapse:collapse;
margin-top:25px;

}

thead{

background:#20262f;

}

th,td{

padding:15px;
text-align:left;
border-bottom:1px solid #2e3640;

}

tbody tr{

transition:.3s;

}

tbody tr:hover{

background:#1f2937;

}

.price{

color:#4ade80;
font-weight:600;

}

.header{

display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:25px;

}

.badge{

background:#2563eb;
padding:8px 15px;
border-radius:30px;
font-size:13px;

}

@media(max-width:900px){

.container{

grid-template-columns:1fr;

}

}

</style>

</head>
<body>

<div class="header">

<h2>E-Commerce Module</h2>

<div class="badge">
Connected Module
</div>

</div>

<div class="container">

<div class="card">

<div class="section-title">
Customer Information
</div>

<label>Customer Name</label>
<input type="text" id="customerName" value="Panda Decoco">

<label>Email</label>
<input type="email" id="customerEmail" value="panda@gmail.com">

<label>Phone Number</label>
<input
type="text"
id="phone"
maxlength="11"
value="09658852674"
pattern="[0-9]{11}"
oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)"
>

</div>

<div class="card">

<div class="section-title">
Customer Orders
</div>

<table id="ordersTable">

<thead>

<tr>

<th>Order ID</th>
<th>Product</th>
<th>Price</th>

</tr>

</thead>

<tbody>

<tr>

<td>ORD 67</td>
<td>Radeon RX 7900 XTX</td>
<td class="price">$999.99</td>

</tr>

<tr>

<td>ORD 50</td>
<td>990 Pro NVMe</td>
<td class="price">$500.00</td>

</tr>

<tr>

<td>ORD 56</td>
<td>NVIDIA GTX 1040</td>
<td class="price">$877.00</td>

</tr>

</tbody>

</table>

<hr style="margin:30px 0;border-color:#30363d;">

<div class="section-title">
Add New Order
</div>

<label>Order ID</label>
<input id="orderID" placeholder="ORD 80">

<label>Product Name</label>
<input id="productName" placeholder="RTX 5090">

<label>Price</label>
<input id="price" type="number" step="0.01" placeholder="999.99">

<button onclick="addOrder()">
Add Order
</button>

</div>

</div>

<script>

function addOrder(){

let id=document.getElementById('orderID').value.trim();
let product=document.getElementById('productName').value.trim();
let price=document.getElementById('price').value.trim();

if(id==''||product==''||price==''){

alert('Please complete all fields.');
return;

}

let tbody=document.querySelector('#ordersTable tbody');

let row=document.createElement('tr');

row.innerHTML=`
<td>${id}</td>
<td>${product}</td>
<td class="price">$${parseFloat(price).toFixed(2)}</td>
`;

tbody.appendChild(row);

document.getElementById('orderID').value='';
document.getElementById('productName').value='';
document.getElementById('price').value='';

}

document.getElementById('phone').addEventListener('blur',function(){

if(this.value.length!==11){

alert('Phone number must contain exactly 11 digits.');
this.focus();

}

});

</script>

</body>
</html>