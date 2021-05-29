
console.log('%c' + 'Loading WoofRestClient', 'color: #0bf; font-size: 1rem; background-color:#fff');

class WoofRestClient
{
  _baseURL;
  _nonce;


  constructor(baseURL, nonce)
  {
    this._baseURL = baseURL;
    this._nonce = nonce;
  }

  async get(url, options = {}) {
    const response = await this.ajax('GET', url, options);
    return response;
  }

  async post(url, data = {}, options = {}) {
    const response = await this.ajax('POST', url, data, options);
    return response;
  }

  async ajax(method, url, data = {},  options = {}) {
    // Default options are marked with *
    let currentOptions = {
      method: method,
      mode: 'cors',
      cache: 'no-cache',
      credentials: 'include', // include, *same-origin, omit handling cookies
      redirect: 'follow', // manual, *follow, error
      referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
    };

    currentOptions.headers = {};

    if(window.WP_API_NONCE) {
      currentOptions.headers['X-WP-Nonce'] = this.nonce;
    }

    if(data) {
      currentOptions.body = JSON.stringify(data); // body data type must match "Content-Type" header
      currentOptions.headers['Content-Type'] = 'application/json';
      // 'Content-Type': 'application/x-www-form-urlencoded',
    }
    const response = await fetch(url, currentOptions);
    return response.json(); // parses JSON response into native JavaScript objects
  }
}
