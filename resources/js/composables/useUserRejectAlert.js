import Swal from 'sweetalert2'

export function useUserRejectAlert() {
  function confirmReject(name) {
    const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
        cancelButton: "btn btn-success mx-2 bg-green-500 text-white rounded-md px-4 py-2", // green
        confirmButton: "btn btn-danger mx-2 bg-red-500 text-white rounded-md px-4 py-2"  // red
      },
      buttonsStyling: false
    });

    const swalWithBlueButton = Swal.mixin({
      customClass: {
        confirmButton: "bg-blue-500 text-white rounded-md px-4 py-2"
      },
      buttonsStyling: false
    });

    return swalWithBootstrapButtons.fire({
      title: `Are you sure you want to reject ${name}?`,
      text: "You won't be able to revert this!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Reject",
      cancelButtonText: "Cancel",
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        swalWithBlueButton.fire({
          title: "Rejected!",
          text: `${name} has been rejected successfully.`,
          icon: "success"
        });
        return true;
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        swalWithBlueButton.fire({
          title: "Cancelled",
          text: `Rejection of ${name} was cancelled.`,
          icon: "error"
        });
        return false;
      }
      return false;
    });
  }
  return { confirmReject };
}