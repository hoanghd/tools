package com.shinway;

import com.google.inject.AbstractModule;
import com.google.inject.Guice;
import com.google.inject.Injector;
import com.shinway.util.InjectorHolder;
import org.mybatis.guice.XMLMyBatisModule;

//http://programmingcat.hatenablog.com/entry/2014/02/27/214901

public class Main {
	private Injector injector;
	
	public Main(){
		final InjectorHolder holder = new InjectorHolder();
        injector = Guice.createInjector(new XMLMyBatisModule() {
            @Override
            protected void initialize() {
                setEnvironmentId("default");
                setClassPathResource("mybatis-config.xml");
            }
        },
        new AbstractModule() {
            @Override
            protected void configure() {
                bind(InjectorHolder.class).toInstance(holder);
            }
        });
	}
	
	public void test(){
		
	}
	
	public static void main(String[] args) {
		Main o = new Main();
		o.test();
		System.out.println("OK");
	}
}
