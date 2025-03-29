import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SplahLogoComponent } from './splah-logo.component';

describe('SplahLogoComponent', () => {
  let component: SplahLogoComponent;
  let fixture: ComponentFixture<SplahLogoComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SplahLogoComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SplahLogoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
