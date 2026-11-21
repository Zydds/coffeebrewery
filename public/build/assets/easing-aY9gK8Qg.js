const n=t=>{const r=(e,o,i)=>3*(1-e)**2*e*o+3*(1-e)*e**2*i+e**3;let s=0,l=1;for(let e=0;e<12;e++){const o=(s+l)/2;r(o,.22,.36)<t?s=o:l=o}return r((s+l)/2,1,1)};export{n as e};
