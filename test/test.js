var glb=require('../js/globals');
var assert = require('assert');
var chai = require('chai');
describe('Globals', function() {
  describe('#removeItemFromFileArray()', function() {
    it('should delete an item when exist or throw "Item not found"', function() {
        var a=[{name:"F1"}, {name:"F2"}, {name:"F3"}];
        var len=a.length;
        glb.FUNCTIONS.removeItemFromFileArray(a, "F1");
        assert.equal(a.length, len-1);
    });
  });
  describe('#removeItemFromFileArray()', function() {
    it('should delete an item when exist or throw "Item not found"', function() {
        var a=[{name:"F1"}, {name:"F2"}, {name:"F3"}];
        var test=function() { glb.FUNCTIONS.removeItemFromFileArray(a, "F4"); };
        chai.expect(test).to.throw(glb.ITEM_NOT_FOUND);
    });
  });
});