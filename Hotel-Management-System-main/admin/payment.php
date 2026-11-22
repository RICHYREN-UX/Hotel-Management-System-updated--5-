<?php
    session_start();
    include '../config.php';
    include 'paymentcols.php';
    ensurePayCols($conn);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sport's Man Arms Hotel - Admin</title>
    <!-- boot -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- fontowesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
	<!-- css for table and search bar -->
	<link rel="stylesheet" href="css/roombook.css">

</head>
<body>
	<div class="searchsection">
        <input type="text" name="search_bar" id="search_bar" placeholder="search..." onkeyup="searchFun()">
    </div>

    <div class="roombooktable" class="table-responsive-xl">
        <?php
            $paymanttablesql = "SELECT p.*, r.stat FROM payment p LEFT JOIN roombook r ON r.id = p.id";
            $paymantresult = mysqli_query($conn, $paymanttablesql);

            $nums = mysqli_num_rows($paymantresult);
        ?>
        <table class="table table-bordered" id="table-data">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Name</th>
                    <th scope="col">Room Type</th>
                    <th scope="col">Bed Type</th>
                    <th scope="col">Check In</th>
                    <th scope="col">Check Out</th>
					<th scope="col">No of Day</th>
                    <th scope="col">No of Room</th>
					<th scope="col">Meal Type</th>
                    <th scope="col">Room Rent</th>
                    <th scope="col">Bed Rent</th>
                    <th scope="col">Meals</th>
					<th scope="col">Total Bill</th>
                    <th scope="col">Payment</th>
                    <th scope="col">Action</th>
                    <!-- <th>Delete</th> -->
                </tr>
            </thead>

            <tbody>
            <?php
            while ($res = mysqli_fetch_array($paymantresult)) {
            ?>
                <tr>
                    <td><?php echo $res['id'] ?></td>
                    <td><?php echo $res['Name'] ?></td>
                    <td><?php echo $res['RoomType'] ?></td>
                    <td><?php echo $res['Bed'] ?></td>
					<td><?php echo $res['cin'] ?></td>
                    <td><?php echo $res['cout'] ?></td>
					<td><?php echo $res['noofdays'] ?></td>
                    <td><?php echo $res['NoofRoom'] ?></td>
                    <td><?php echo $res['meal'] ?></td>
                    <td><?php echo $res['roomtotal'] ?></td>
					<td><?php echo $res['bedtotal'] ?></td>
					<td><?php echo $res['mealtotal'] ?></td>
					<td><?php echo $res['finaltotal'] ?></td>
                    <td>
                        <?php if ($res['stat'] == 'Paid') { ?>
                            <b><?php echo htmlspecialchars($res['paymethod'] ?? 'Paid') ?></b>
                            <?php if (!empty($res['payref'])) { ?><br><small>Ref: <?php echo htmlspecialchars($res['payref']) ?></small><?php } ?>
                            <?php if (!empty($res['paiddate'])) { ?><br><small><?php echo $res['paiddate'] ?></small><?php } ?>
                        <?php } else { ?>
                            <span class="badge bg-warning text-dark">Not Paid</span>
                        <?php } ?>
                    </td>
                    <td class="action">
                        <?php if ($res['stat'] == 'Paid') { ?>
                            <button class="btn btn-secondary" disabled>Paid</button>
                        <?php } else { ?>
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#payModal" data-id="<?php echo $res['id'] ?>" data-name="<?php echo htmlspecialchars($res['Name']) ?>">Paid</button>
                        <?php } ?>
                        <a href="invoiceprint.php?id=<?php echo $res['id']?>"><button class="btn btn-primary"><i class="fa-solid fa-print"></i>Print</button></a>
						<a href="paymantdelete.php?id=<?php echo $res['id']?>"><button class="btn btn-danger">Delete</button></a>
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </div>

<!-- Payment method popup -->
<div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" method="POST" action="paymentconfirm.php">
      <div class="modal-header">
        <h5 class="modal-title">Confirm payment - <span id="payGuest"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="payId">
        <input type="hidden" name="from" value="payment">
        <label class="form-label">Payment method</label>
        <select name="method" id="payMethod" class="form-select mb-3" onchange="toggleRef()">
          <option>Cash</option>
          <option>M-Pesa</option>
          <option>Card</option>
          <option>Bank Transfer</option>
        </select>
        <div id="refBox" style="display:none;">
          <label class="form-label">Reference / transaction code</label>
          <input type="text" name="reference" id="payRef" class="form-control" maxlength="60" placeholder="e.g. QWE1234XYZ">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Confirm Paid</button>
      </div>
    </form>
  </div>
</div>
<script>
  function toggleRef(){
    var m = document.getElementById('payMethod').value;
    document.getElementById('refBox').style.display = (m === 'Cash') ? 'none' : 'block';
    if (m === 'Cash') { document.getElementById('payRef').value = ''; }
  }
  document.getElementById('payModal').addEventListener('show.bs.modal', function (e) {
    var b = e.relatedTarget;
    document.getElementById('payId').value = b.getAttribute('data-id');
    document.getElementById('payGuest').textContent = b.getAttribute('data-name');
    document.getElementById('payMethod').value = 'Cash';
    toggleRef();
  });
</script>
</body>

<script>
    //search bar logic using js
    const searchFun = () =>{
        let filter = document.getElementById('search_bar').value.toUpperCase();

        let myTable = document.getElementById("table-data");

        let tr = myTable.getElementsByTagName('tr');

        for(var i = 0; i< tr.length;i++){
            let td = tr[i].getElementsByTagName('td')[1];

            if(td){
                let textvalue = td.textContent || td.innerHTML;

                if(textvalue.toUpperCase().indexOf(filter) > -1){
                    tr[i].style.display = "";
                }else{
                    tr[i].style.display = "none";
                }
            }
        }

    }

</script>

</html>