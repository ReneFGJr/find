import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SocialsMenuComponent } from './socials-menu.component';

describe('SocialsMenuComponent', () => {
  let component: SocialsMenuComponent;
  let fixture: ComponentFixture<SocialsMenuComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [SocialsMenuComponent]
    });
    fixture = TestBed.createComponent(SocialsMenuComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
