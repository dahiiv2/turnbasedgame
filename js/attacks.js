class Attack {
    constructor(name, baseDamage, damageVariance = 0, specialEffect = null) {
      this.name = name;
      this.baseDamage = baseDamage;
      this.damageVariance = damageVariance;
      this.specialEffect = specialEffect;
    }
  
    calculateDamage() {
      const variance = Math.floor(Math.random() * this.damageVariance);
      return this.baseDamage + variance;
    }
  
    applySpecialEffect(target) {
      if (this.specialEffect) {
        this.specialEffect(target);
      }
    }
  }
  
  const attacks = {
    slash: new Attack('Slash', 5, 2),
    focus: new Attack('Focus', 0, 0),
    bladestorm: new Attack('Bladestorm', 22, 0)
  };
  
  export default attacks;
  