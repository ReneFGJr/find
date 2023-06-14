import { ComponentFixture, TestBed } from '@angular/core/testing';

import { HomeIndexComponent } from './home-index.component';

describe('HomeIndexComponent', () => {
  let component: HomeIndexComponent;
  let fixture: ComponentFixture<HomeIndexComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [HomeIndexComponent]
    });
    fixture = TestBed.createComponent(HomeIndexComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
