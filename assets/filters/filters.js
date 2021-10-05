import Vue from "vue"
import moment from "moment"

Vue.filter('momentAgo', function (date) {
    return moment(date).fromNow();
})
Vue.filter('arrayDifference', function (arr1, arr2) {
    return [
        ...arr1.filter(x => !arr2.includes(x)),
        ...arr2.filter(x => !arr1.includes(x))
    ];
})
Vue.filter('objectDifference', function (obj1, obj2) {
    return this.arrayDifference(Object.keys(obj1), Object.keys(obj2))
})
