export default async ({ Vue }) => {
  Vue.prototype.$fingerPrint = (text) => {
    return new Promise((resolve, reject) => {
      window.Fingerprint.isAvailable((suc) => {
        window.Fingerprint.show({
          clientId: 'fingerprintId',
          clientSecret: 'o7aoOMYUbyxaD23oFAnJ',
          disableBackup: true
        }, (suc) => {
          resolve(suc)
        }, (err) => {
          reject(err)
        })
      }, () => {
        reject(false)
      })
    })
  }

  Vue.prototype.$RegisterFP = (text) => {
    return new Promise((resolve, reject) => {
      // window.Fingerprint.registerBiometricSecret({
      //   title: "Capture d'empreinte",
      //   description: "Veuillez scanner votre pouce droite",
      //   secret: '1234',
      //   invalidateOnEnrollment: true,
      //   disableBackup: true, // always disabled on Android
      // }, successCallback, errorCallback);
  
      // function successCallback(){
      //   alert("Authentication successful" + secret);
      // }
  
      // function errorCallback(error){
      //   alert("Authentication invalid " + error.message);
      // }

      window.Fingerprint.registerBiometricSecret({
        secret: text,
        invalidateOnEnrollment: true,
        disableBackup: true, // always disabled on Android
      });

      window.Fingerprint.loadBiometricSecret({
        title: "Capture d'empreinte",
        description: "Veuillez scanner votre pouce droite",
        disableBackup: true, // always disabled on Android
      }, successCallback, errorCallback);
  
      function successCallback(secret){
        alert("Authentication successful, secret: " + secret);
      }
  
      function errorCallback(error){
        alert("Authentication invalid " + error.message);
      }

      // window.Fingerprint.isAvailable((suc) => {
      //   window.Fingerprint.show({
      //     clientId: 'fingerprintId',
      //     clientSecret: 'o7aoOMYUbyxaD23oFAnJ',
      //     disableBackup: true
      //   }, (suc) => {
      //     resolve(suc)
      //   }, (err) => {
      //     reject(err)
      //   })
      // }, () => {
      //   reject(false)
      // })
    })
  }
}
