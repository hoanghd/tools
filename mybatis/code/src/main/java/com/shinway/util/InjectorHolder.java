package com.shinway.util;

import com.google.inject.Injector;

public class InjectorHolder {
	private Injector injector;  
  
	public InjectorHolder() {
	}
  
	public void setInjector(Injector injector) {
	    assert this.injector == null && injector != null;
	    this.injector = injector;
	}

	public <T> T getInstance(Class<T>type) {
	    return injector.getInstance(type);
	}
}
