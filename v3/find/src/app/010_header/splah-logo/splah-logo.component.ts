import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';

@Component({
  selector: 'app-splah-logo',
  imports: [],
  templateUrl: './splah-logo.component.html',
  styleUrl: './splah-logo.component.scss'
})
export class SplashLogoComponent implements OnInit, AfterViewInit {
  @ViewChild('logoRef') logoRef!: ElementRef;
  @ViewChild('brandRef') brandRef!: ElementRef;

  logoStyle: any = {};
  startAnimation = false;

  ngOnInit(): void {
    setTimeout(() => {
      this.startAnimation = true;
      this.moveLogo();
    }, 2000); // espera 2 segundos
  }

  ngAfterViewInit(): void {
    // garante que o DOM está pronto
  }

  moveLogo() {
    const logoEl = this.logoRef.nativeElement;
    const brandEl = this.brandRef.nativeElement;

    const logoRect = logoEl.getBoundingClientRect();
    const brandRect = brandEl.getBoundingClientRect();

    const deltaX = brandRect.left - logoRect.left;
    const deltaY = brandRect.top - logoRect.top;

    this.logoStyle = {
      transform: `translate(${deltaX}px, ${deltaY}px) scale(0.4)`,
    };
  }
}
